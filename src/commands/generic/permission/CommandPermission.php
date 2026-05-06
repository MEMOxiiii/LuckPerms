<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\commands\generic\permission;

use function array_map;

class CommandPermission{

	private string $permission;

	private function __construct(string $permission){
		$this->permission = $permission;
	}

	public function getPermission() : string{
		return $this->permission;
	}

	/** @return self[] */
	public static function getAll() : array{
		static $all = null;
		if($all === null){
			$nodes = [
				'luckperms.sync', 'luckperms.info', 'luckperms.editor', 'luckperms.debug',
				'luckperms.verbose', 'luckperms.tree', 'luckperms.search', 'luckperms.check',
				'luckperms.import', 'luckperms.export', 'luckperms.reload', 'luckperms.bulkupdate',
				'luckperms.migrations', 'luckperms.apidocs',
				'luckperms.user.info', 'luckperms.user.editor', 'luckperms.user.promote',
				'luckperms.user.demote', 'luckperms.user.showtracks', 'luckperms.user.clear',
				'luckperms.user.clone',
				'luckperms.user.permission.info', 'luckperms.user.permission.set',
				'luckperms.user.permission.unset', 'luckperms.user.permission.settemp',
				'luckperms.user.permission.unsettemp', 'luckperms.user.permission.check',
				'luckperms.user.permission.clear',
				'luckperms.user.parent.info', 'luckperms.user.parent.set',
				'luckperms.user.parent.add', 'luckperms.user.parent.remove',
				'luckperms.user.parent.settrack', 'luckperms.user.parent.addtemp',
				'luckperms.user.parent.removetemp', 'luckperms.user.parent.clear',
				'luckperms.user.parent.cleartrack', 'luckperms.user.parent.switchprimarygroup',
				'luckperms.user.meta.info', 'luckperms.user.meta.set', 'luckperms.user.meta.unset',
				'luckperms.user.meta.settemp', 'luckperms.user.meta.unsettemp',
				'luckperms.user.meta.addprefix', 'luckperms.user.meta.addsuffix',
				'luckperms.user.meta.setprefix', 'luckperms.user.meta.setsuffix',
				'luckperms.user.meta.removeprefix', 'luckperms.user.meta.removesuffix',
				'luckperms.user.meta.addtempprefix', 'luckperms.user.meta.addtempsuffix',
				'luckperms.user.meta.settempprefix', 'luckperms.user.meta.settempsuffix',
				'luckperms.user.meta.removetempprefix', 'luckperms.user.meta.removetempsuffix',
				'luckperms.user.meta.clear',
				'luckperms.group.info', 'luckperms.group.editor', 'luckperms.group.listmembers',
				'luckperms.group.setweight', 'luckperms.group.setdisplayname',
				'luckperms.group.showtracks', 'luckperms.group.clear', 'luckperms.group.clone',
				'luckperms.group.rename', 'luckperms.group.create', 'luckperms.group.delete',
				'luckperms.group.list',
				'luckperms.group.permission.info', 'luckperms.group.permission.set',
				'luckperms.group.permission.unset', 'luckperms.group.permission.settemp',
				'luckperms.group.permission.unsettemp', 'luckperms.group.permission.check',
				'luckperms.group.permission.clear',
				'luckperms.group.parent.info', 'luckperms.group.parent.set',
				'luckperms.group.parent.add', 'luckperms.group.parent.remove',
				'luckperms.group.parent.settrack', 'luckperms.group.parent.addtemp',
				'luckperms.group.parent.removetemp', 'luckperms.group.parent.clear',
				'luckperms.group.parent.cleartrack',
				'luckperms.group.meta.info', 'luckperms.group.meta.set',
				'luckperms.group.meta.unset', 'luckperms.group.meta.settemp',
				'luckperms.group.meta.unsettemp', 'luckperms.group.meta.addprefix',
				'luckperms.group.meta.addsuffix', 'luckperms.group.meta.setprefix',
				'luckperms.group.meta.setsuffix', 'luckperms.group.meta.removeprefix',
				'luckperms.group.meta.removesuffix', 'luckperms.group.meta.addtempprefix',
				'luckperms.group.meta.addtempsuffix', 'luckperms.group.meta.settempprefix',
				'luckperms.group.meta.settempsuffix', 'luckperms.group.meta.removetempprefix',
				'luckperms.group.meta.removetempsuffix', 'luckperms.group.meta.clear',
				'luckperms.track.info', 'luckperms.track.editor', 'luckperms.track.append',
				'luckperms.track.insert', 'luckperms.track.remove', 'luckperms.track.clear',
				'luckperms.track.rename', 'luckperms.track.create', 'luckperms.track.delete',
				'luckperms.track.list',
				'luckperms.log.recent', 'luckperms.log.search', 'luckperms.log.notify',
				'luckperms.log.userhistory', 'luckperms.log.grouphistory', 'luckperms.log.trackhistory',
			];
			$all = array_map(fn($node) => new self($node), $nodes);
		}
		return $all;
	}
}
