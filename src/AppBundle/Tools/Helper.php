<?php
namespace AppBundle\Tools;


class Helper {
	public static function fixUuid($uuid){
		$uuid = str_replace(' ', '+', $uuid);
		return $uuid;
	}
}