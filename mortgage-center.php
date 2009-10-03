<?
/*
Plugin Name: Mortgage Center
Plugin URI: http://wordpress.org/extend/plugins/mortgage-center/
Description: This plugin allows WordPress to load mortgage-related data from Zillow and Closing.com.
Version: pre-1.0
Author: Andrew Mattie
*/

/*  Copyright 2009, Andrew Mattie

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
register_activation_hook(__FILE__, 'MortgageCenter::CreateOptions');

class MortgageCenter {
	function CreateOptions() {
		add_option('mc-state', 'CA');
		add_option('mc-url-slug', 'mortgage');
	}
}
?>