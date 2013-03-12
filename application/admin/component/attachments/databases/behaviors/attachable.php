<?php

use Nooku\Framework;

class AttachmentsDatabaseBehaviorAttachable extends Framework\DatabaseBehaviorAbstract
{
	public function getAttachments()
	{
		return $this->getService('com://admin/attachments.model.attachments')
				->row($this->id)
				->table($this->getTable()->getBase())
				->getRowset();
	}
}