<?php
/** $Id$ */

class PlgEventDebug extends KEventListener
{
	public $queries = array();
	
	public function update(KConfig $args)
	{		
		if(KDEBUG) 
		{
			KFactory::get('lib.koowa.profiler')->mark( $args->event );
			return parent::update($args);
		}
	}
	
	public function onDatabaseAfterSelect(KCommandContext $context)
	{
		$this->queries[] = $context->query;
	}
	
	public function onDatabaseAfterUpdate(KCommandContext $context)
	{
		$this->queries[] = $context->query;
	}
		
	public function onDatabaseAfterInsert(KCommandContext $context)
	{
		$this->queries[] = $context->query;
	}
		
	public function onDatabaseAfterDelete(KCommandContext $context)
	{
		$this->queries[] = $context->query;
	}
	
	public function onDatabaseAfterShow(KCommandContext $context)
	{
		$this->queries[] = $context->query;
	}
}

