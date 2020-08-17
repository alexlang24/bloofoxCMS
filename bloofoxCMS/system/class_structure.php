<?php
//*****************************************************************//
// This file is part of bloofoxCMS! Do not delete this copyright!!!
// - system/class_structure.php -
//
// Copyrights (c) 2006-2009 Alexander Lang, Germany
// info@bloofox.com
// http://www.bloofox.com
//
// bloofoxCMS is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// any later version.
//
// bloofoxCMS is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//*****************************************************************//

// Forbid direct call
if(!defined('SYS_INDEX')) {
	die("You can't call this file directly!");
}

class structure {
	//**
	// variables
	var $sort_distance = 0; // distance between 2 rows
	var $level = 1; // default level
	var $s_array = array(); // array s values
	
	//**
	// constructor
	function __construct($sort_distance)
	{
		$this->sort_distance = $sort_distance;
		$this->s_array['level'] = $this->level;
		$this->s_array['preid'] = 0;
		$this->s_array['sorting'] = 0;
		$this->s_array['config_id'] = 0;
	}

	//***
	// private functions
	//*
	
	//**
	// set level in s_array
	function set_level($level)
	{
		if(empty($level)) {
			return;
		}
		$this->s_array['level'] = $level;
	}
	
	//**
	// set preid in s_array
	function set_preid($preid)
	{
		if(empty($preid)) {
			return;
		}
		$this->s_array['preid'] = $preid;
	}
	
	//**
	// set sorting in s_array
	function set_sorting($sorting)
	{
		if(empty($sorting)) {
			$sorting = $this->sort_distance;
		}
		$this->s_array['sorting'] = $sorting;
	}
	
	//**
	// set config_id in s_array
	function set_config_id($config_id)
	{
		if(empty($config_id)) {
			return;
		}
		$this->s_array['config_id'] = $config_id;
	}
	
	//**
	// get level from eid: return level
	function get_level($db,$eid)
	{
		global $tbl_prefix;
		
		$db->query("SELECT level FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$level = $db->f("level");
		endwhile;
		
		return($level);
	}

	//**
	// get preid from eid: return preid
	function get_preid($db,$eid)
	{
		global $tbl_prefix;
		
		$db->query("SELECT preid FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$preid = $db->f("preid");
		endwhile;
		
		return($preid);
	}
	
	//**
	// get config_id from eid: return config_id
	function get_config_id($db,$eid)
	{
		global $tbl_prefix;
		
		$db->query("SELECT config_id FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$config_id = $db->f("config_id");
		endwhile;
		
		return($config_id);
	}
	
	//**
	// get sorting from eid: return sorting
	function get_sorting($db,$eid)
	{
		global $tbl_prefix;
		
		$db->query("SELECT sorting FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$sorting = $db->f("sorting");
		endwhile;
		
		return($sorting);
	}
	
	//**
	// get lower_id from eid: return lower_id
	function get_lower_id($db,$eid,$level,$preid)
	{
		global $tbl_prefix;
		$next = 0;
		
		$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE level = '".$level."' && preid = '".$preid."' ORDER BY sorting");
		while($db->next_record()):
			if($next == 1) {
				$lower_id = $db->f("eid");
				$next = 0;
			}
			if($db->f("eid") == $eid) {
				$next = 1;
			}
		endwhile;
		
		return($lower_id);
	}
	
	//**
	// get underneath_id from eid: return underneath_id
	function get_underneath_id($db,$eid,$level)
	{
		global $tbl_prefix;
		
		$db->query("SELECT eid FROM ".$tbl_prefix."sys_explorer WHERE level = '".$level."' && preid = '".$eid."' ORDER BY sorting LIMIT 1");
		while($db->next_record()):
			$underneath_id = $db->f("eid");
		endwhile;
		
		return($underneath_id);
	}
	
	//***
	// public functions
	//*
	
	//**
	// insert a new line to content explorer
	function new_line($db,$upper_id,$where=0)
	{
		global $tbl_prefix;
		
		// get upperline's level and set new level
		$upper_level = $this->get_level($db,$upper_id);
		if($upper_level == 1) {
			$where = 1;
		}
		if($where == 1) {
			$upper_level += 1;
		}
		$this->set_level($upper_level);
		
		// get upperline's preid and set new preid
		if($where == 1) {
			$this->set_preid($upper_id);
		} else {
			$upper_preid = $this->get_preid($db,$upper_id);
			$this->set_preid($upper_preid);
		}
		
		// get upperline's config_id and set new config_id
		$upper_config_id = $this->get_config_id($db,$upper_id);
		$this->set_config_id($upper_config_id);
		
		// get upperline's sorting and set new sorting
		if($where == 1) {
			$underneath_id = $this->get_underneath_id($db,$upper_id,$upper_level);
			$underneath_sorting = $this->get_sorting($db,$underneath_id);
			if(empty($underneath_sorting)) {
				$this->set_sorting($this->sort_distance);
			} else {
				$this->set_sorting(round($underneath_sorting/2));
			}
		} else {
			$upper_sorting = $this->get_sorting($db,$upper_id);
			$lower_id = $this->get_lower_id($db,$upper_id,$upper_level,$upper_preid);
			$lower_sorting = $this->get_sorting($db,$lower_id);
			if(empty($lower_sorting)) {
				$this->set_sorting(($upper_sorting+$this->sort_distance));
			} else {
				$this->set_sorting(round(($upper_sorting+$lower_sorting)/2));
			}
		}
		
		return($this->s_array);
	}

	//** private
	// get lower_entry from eid: return eid, level, sorting, preid
	function get_lower_entry($db,$eid,$level,$preid,$sorting)
	{
		global $tbl_prefix;
		
		$db->query("SELECT eid,level,preid,sorting FROM ".$tbl_prefix."sys_explorer WHERE level = '".$level."' && preid = '".$preid."' && sorting > '".$sorting."' ORDER BY sorting LIMIT 1");
		while($db->next_record()):
			$entry['eid'] = $db->f("eid");
			$entry['level'] = $db->f("level");
			$entry['preid'] = $db->f("preid");
			$entry['sorting'] = $db->f("sorting");
		endwhile;
		
		return($entry);
	}
	
	//**
	// get upper_entry from eid: return eid, level, sorting, preid
	function get_upper_entry($db,$eid,$level,$preid,$sorting)
	{
		global $tbl_prefix;
		
		$db->query("SELECT eid,level,preid,sorting FROM ".$tbl_prefix."sys_explorer WHERE level = '".$level."' && preid = '".$preid."' && sorting < '".$sorting."' ORDER BY sorting DESC LIMIT 1");
		while($db->next_record()):
			$entry['eid'] = $db->f("eid");
			$entry['level'] = $db->f("level");
			$entry['preid'] = $db->f("preid");
			$entry['sorting'] = $db->f("sorting");
		endwhile;
		
		return($entry);
	}
	
	//**
	// get current entry from eid: return level,preid,sorting
	function get_current_entry($db,$eid)
	{
		global $tbl_prefix;
		
		$db->query("SELECT level,preid,sorting FROM ".$tbl_prefix."sys_explorer WHERE eid = '".$eid."' ORDER BY eid LIMIT 1");
		while($db->next_record()):
			$entry['level'] = $db->f("level");
			$entry['preid'] = $db->f("preid");
			$entry['sorting'] = $db->f("sorting");
		endwhile;
		
		return($entry);
	}
	
	//**
	// move functions
	function move_left($db,$eid)
	{
		global $tbl_prefix;
		
		// get curent entry
		$curr_entry = $this->get_current_entry($db,$eid);
		// get current previous entry
		$prev_entry = $this->get_current_entry($db,$curr_entry['preid']);
		// get entry below prev entry
		$lower_entry = $this->get_lower_entry($db,$curr_entry['preid'],$prev_entry['level'],$prev_entry['preid'],$prev_entry['sorting']);
		
		// exit if it reachs level 1
		if($prev_entry['level'] < 2) {
			return;
		}
		
		// calculate new sorting
		if(empty($lower_entry['sorting'])) {
			$new_sorting = $prev_entry['sorting'] + $this->sort_distance;
		} else {
			$new_sorting = round(($prev_entry['sorting'] + $lower_entry['sorting'])/2);
		}
		
		// update curr entry's preid, level and sorting
		$db->query("UPDATE ".$tbl_prefix."sys_explorer SET preid = '".$prev_entry['preid']."',sorting = '".$new_sorting."',level = ".$prev_entry['level']." WHERE eid = '".$eid."' LIMIT 1");
		// update underneath levels (all entries level-1)
		//-> should be a function!!!
		$db->query("UPDATE ".$tbl_prefix."sys_explorer SET level = level-1 WHERE preid = '".$eid."'");
		
		CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
	}
	
	function move_right($db,$eid)
	{
		global $tbl_prefix;
		
		// get curent entry
		$curr_entry = $this->get_current_entry($db,$eid);
		// get current upper entry
		$upper_entry = $this->get_upper_entry($db,$eid,$curr_entry['level'],$curr_entry['preid'],$curr_entry['sorting']);
		// get last entry underneath upper entry
		$underneath_eid = $this->get_underneath_id($db,$upper_entry['eid'],$upper_entry['level']+1);
		$first_sorting = $this->get_sorting($db,$underneath_eid);
		
		// exit if no upper entry exists
		if(empty($upper_entry['level'])) {
			return;
		}
		
		// calculate new sorting
		if(empty($first_sorting)) {
			$new_sorting = $this->sort_distance;
		} else {
			$new_sorting = round($first_sorting/2);
		}
		
		// update current entry's level,preid,sorting
		$db->query("UPDATE ".$tbl_prefix."sys_explorer SET preid = '".$upper_entry['eid']."',sorting = '".$new_sorting."',level = level+1 WHERE eid = '".$eid."' LIMIT 1");
		// update underneath levels (all entries level+1)
		//-> should be a function!!!
		$db->query("UPDATE ".$tbl_prefix."sys_explorer SET level = level+1 WHERE preid = '".$eid."'");
		
		CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
	}
	
	function move_up($db,$eid)
	{
		global $tbl_prefix;
		
		$curr_entry = $this->get_current_entry($db,$eid);
		// get upper entry with same level and same preid, return sorting
		$upper_entry1 = $this->get_upper_entry($db,$eid,$curr_entry['level'],$curr_entry['preid'],$curr_entry['sorting']);
		// get upper entry of upper entry with same level and same preid, return sorting
		$upper_entry2 = $this->get_upper_entry($db,$upper_entry1['eid'],$curr_entry['level'],$curr_entry['preid'],$upper_entry1['sorting']);
		// change sorting
		if(empty($upper_entry1['sorting'])) {
			return;
		}
		$new_sorting = round(($upper_entry1['sorting'] + $upper_entry2['sorting'])/2);
		if(!empty($new_sorting)) {
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET sorting = '".$new_sorting."' WHERE eid = '".$eid."' LIMIT 1");
			CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
		}
	}
	
	function move_down($db,$eid)
	{
		global $tbl_prefix;
		
		$curr_entry = $this->get_current_entry($db,$eid);
		// get lower entry with same level and preid, return sorting
		$lower_entry1 = $this->get_lower_entry($db,$eid,$curr_entry['level'],$curr_entry['preid'],$curr_entry['sorting']);
		// get lower entry of lower entry with same level and preid, return sorting
		$lower_entry2 = $this->get_lower_entry($db,$lower_entry1['eid'],$curr_entry['level'],$curr_entry['preid'],$lower_entry1['sorting']);
		// change sorting
		if(empty($lower_entry1['sorting'])) {
			return;
		}
		if(empty($lower_entry2['sorting'])) {
			$new_sorting = $lower_entry1['sorting'] + $this->sort_distance;
		} else {
			$new_sorting = round(($lower_entry1['sorting'] + $lower_entry2['sorting'])/2);
		}
		if(!empty($new_sorting)) {
			$db->query("UPDATE ".$tbl_prefix."sys_explorer SET sorting = '".$new_sorting."' WHERE eid = '".$eid."' LIMIT 1");
			CreateConfirmMessage(1,get_caption("0390","Changes have been saved."));
		}
	}
}
?>