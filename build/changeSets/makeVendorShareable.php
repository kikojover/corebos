<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class makeVendorShareable extends cbupdaterWorker {
	
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$vendorsInstance = Vtiger_Module::getInstance('Vendors');
			$blockInstance = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $vendorsInstance);
			$field = Vtiger_Field::getInstance('assigned_user_id',$vendorsInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'assigned_user_id';
				$field->label= 'Assigned To';
				$field->table = 'vtiger_crmentity';
				$field->column = 'smownerid';
				$field->columntype = 'INT(11)';
				$field->uitype = 53;
				$field->displaytype = 1;
				$field->typeofdata = 'V~M';
				$field->presence = 2;
				$field->quickcreate = 0;
				$field->quicksequence = 5;
				$blockInstance->addField($field);
				// Allow Sharing access and role-based security for Vendors
				Vtiger_Access::deleteSharing($vendorsInstance);
				Vtiger_Access::initSharing($vendorsInstance);
				Vtiger_Access::allowSharing($vendorsInstance);
				Vtiger_Access::setDefaultSharing($vendorsInstance);
			}
			
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}