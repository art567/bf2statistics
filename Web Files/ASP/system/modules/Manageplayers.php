<?php
class Manageplayers
{
	public function Init() 
	{
		// Check for post data
		if(isset($_GET['ajax']))
		{
			switch($_GET['ajax'])
			{
				case "list":
					$this->displayPlayerList();
					break;
					
				case "player":
					$this->processPlayer($_POST['id']);
					break;
					
				case "action":
					$this->processAction($_POST['action'], $_POST['id']);
					break;
			}
		}
		else
		{
			// Setup the template
			$Template = load_class('Template');
			$Template->render('manageplayers');
		}
	}
	
	public function displayPlayerList()
	{
		/*
		 * Script:    DataTables server-side script for PHP and MySQL
		 * Copyright: 2010 - Allan Jardine
		 * License:   GPL v2 or BSD (3-point)
		 */
		
		/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 * Easy set variables
		 */
		
		/* Array of database columns which should be read and sent back to DataTables. Use a space where
		 * you want to insert a non-database field (for example a counter or static image)
		 */
		$aColumns = array( 'id', 'name', 'clantag', 'rank', 'score', 'country', 'permban' );
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
		
		/* DB table to use */
		$sTable = "player";
		
		// Load config
		$Config = load_class('Config');
		
		/* Database connection information */
		$gaSql['user']       = $Config->get('db_user');
		$gaSql['password']   = $Config->get('db_pass');
		$gaSql['db']         = $Config->get('db_name');
		$gaSql['server']     = $Config->get('db_host');
		$gaSql['port']       = $Config->get('db_port');
		
		
		/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
		 * no need to edit below this line
		 */
		
		/* 
		 * MySQL connection
		 */
		$gaSql['link'] =  mysql_pconnect( $gaSql['server'] .':'. $gaSql['port'], $gaSql['user'], $gaSql['password']  ) or
			die( 'Could not open connection to server' );
		
		mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
			die( 'Could not select database '. $gaSql['db'] );
		
		
		/* 
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
		}
		
		
		/*
		 * Ordering
		 */
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
						".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && isset($_GET['sSearch_'.$i] ) && $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
			}
		}
		
		/* AI Filtering */
		if($Config->get('admin_ignore_ai'))
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= " isbot = 0 ";
		}
		
		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable
			$sWhere
			$sOrder
			$sLimit
		";
		$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
		
		/* Data set length after filtering */
		$sQuery = "
			SELECT FOUND_ROWS()
		";
		$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
		$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
		$iFilteredTotal = $aResultFilterTotal[0];
		
		/* Total data set length */
		$sQuery = "
			SELECT COUNT(".$sIndexColumn.")
			FROM   $sTable
		";
		$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
		$aResultTotal = mysql_fetch_array($rResultTotal);
		$iTotal = $aResultTotal[0];
		
		
		/*
		 * Output
		 */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] == "version" )
				{
					/* Special output formatting for 'version' column */
					$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
				}
				else if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					$row[] = $aRow[ $aColumns[$i] ];
				}
			}
			
			// Fix name and clantag for special characters
			$row[1] = htmlspecialchars($row[1]);
			$row[2] = htmlspecialchars($row[2]);
			
			// Add manage button and country flag... also fancy little permban yes/no
			$C = ($row[5] == '') ? 'US' : strtoupper($row[5]);
			$flag = $C .'.png';
			$row[5] = '<img src="frontend/images/flags/'. $flag .'" height="16" width="24" alt="'. $C .'">';
			$row[6] = ($row[6] == 1) ? '<font color="red">Yes</font>' : '<font color="green">No</font>';
			$row[] = "<a id='edit' name='". $row[0] ."|". $row[1]."' href='#'>Manage</a>";
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	public function processPlayer($pid)
	{
		// Load the database
		$DB = load_database();
		
		// Process action
		switch($_POST['action'])
		{
			case "fetch":
				// Get the player
				$query = "SELECT `name`, `rank`, `permban`, `clantag` FROM player WHERE id = ". mysql_real_escape_string($pid);
				$result = $DB->query( $query )->result();
				if(!$result)
				{
					echo json_encode( array('success' => false, 'message' => "Player ID ($pid) Does Not Exist!") );
					die();
				}
				
				echo json_encode( array('success' => true) + $DB->fetch_row() );
				break;
				
			case "update":
				// Get unlocks
				$query = "SELECT `availunlocks`, `usedunlocks` FROM `player` WHERE `id` = ". mysql_real_escape_string($pid);
				$unlocks = $DB->query( $query )->fetch_row();
				if(!$unlocks)
				{
					echo json_encode( array('success' => false, 'message' => "Player ID ($pid) Does Not Exist!") );
					die();
				}

				// Reset Unlocks
				if(isset($_POST['reset']) && $_POST['reset'] == 'on')
				{
					$query = "UPDATE `unlocks` SET `state` = 'n' WHERE id = ". mysql_real_escape_string($pid);
					$result = $DB->query( $query )->result();
					if(!$result)
					{
						echo json_encode( array('success' => false, 'message' => "Failed to update player ID ($pid)") );
						die();
					}
					$unlocks['availunlocks'] = $unlocks['availunlocks'] + $unlocks['usedunlocks'];
					$unlocks['usedunlocks'] = 0;
				}

				// Save the player
				$query = "UPDATE `player` SET 
					`rank` = ". mysql_real_escape_string($_POST['rank']) .",
					`availunlocks` = {$unlocks['availunlocks']}, 
					`usedunlocks` = {$unlocks['usedunlocks']},				
					`permban` = ". mysql_real_escape_string($_POST['permban']) .", 
					`clantag` = '". mysql_real_escape_string($_POST['clantag']) ."'
					WHERE id = ". mysql_real_escape_string($pid);
				$result = $DB->query( $query )->result();
				if(!$result)
				{
					echo json_encode( array('success' => false, 'message' => "Failed to update player ID ($pid)") );
					die();
				}
				
				echo json_encode( array('success' => true, 'message' => 'Player Updated Successfully!') );
				break;
		}
	}
	
	public function processAction($action, $pid)
	{
		// Load our player class
		$Player = load_class('Player');

		// Switch to our actions
		switch($action)
		{
				
			case "delete":
				echo json_encode( array('success' => $Player->deletePlayer($pid)) );
				break;
		}
	}
}
?>