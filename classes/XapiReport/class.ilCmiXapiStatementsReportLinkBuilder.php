<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilCmiXapiStatmentsAggregateLinkBuilder
 *
 * @author      Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @author      Björn Heyser <info@bjoernheyser.de>
 * @author      Stefan Schneider <info@eqsoft.de>
 *
 * @package     Module/CmiXapi
 */
class ilCmiXapiStatementsReportLinkBuilder extends ilCmiXapiAbstractReportLinkBuilder
{
    /**
     * @return array
     */
    protected function buildPipeline() : array
    {
	
        $pipeline = array();
        $obj = $this->getObj();
                
        $log = ilLoggerFactory::getLogger('cmix');
               
        $params='activity='.$obj->getActivityId();
        if($this->filter->getVerb()!=''){
        	$params.='&verb='.$this->filter->getVerb();
        }
        if($this->filter->getStartDate() || $this->filter->getEndDate()) {          
            if ($this->filter->getStartDate()) {
                $params.='&since='.$this->filter->getStartDate()->toXapiTimestamp();
            }
            if ($this->filter->getEndDate()) {
                $params.='&until='.$this->filter->getEndDate()->toXapiTimestamp();
            }
        }
        if ($this->filter->getActor()){
        
        	if($obj->getContentType() == ilObjCmiXapi::CONT_TYPE_CMI5){
        	  	$params.='&agent={"account":{"homePage":"http://'.str_replace('www.', '', $_SERVER['HTTP_HOST']).'","name":"'.$this->filter->getActor()->getUsrIdent().'"}}';
        	}
        	else{
        		$params.='&agent={"mbox":"mailto:'.$this->filter->getActor()->getUsrIdent().'"}';
        	}
        }
        if ($this->orderingField()=='dateAsc'){$params.='&ascending=true';}
                
        $pipeline=array($params.'&related_activities='.$this->buildRelatedActivities().'&limit=0');
        return $pipeline;
    }

protected function buildActivityId()
    {
    	$obj = $this->getObj();
    	return $obj->getActivityId();
    }
	
protected function buildRelatedActivities()
    {
    	return 'true';
    }
	
public function orderingField(){
    ilObjCmiXapi::log()->debug('Dans OrderingFields');
    switch ($this->filter->getOrderField()) {
            case 'object': // definition/description are displayed in the Table if not empty => sorting not alphabetical on displayed fields
                ilObjCmiXapi::log()->debug('tri par objet');
                $column = 'objet';
                ilUtil::sendInfo("Le tri par $column n'est pas disponible");
                break;
                
            case 'verb':
            ilObjCmiXapi::log()->debug('tri par verbe');
                $column = 'verbe';
                ilUtil::sendInfo("Le tri par $column n'est pas disponible");
                break;
                
            case 'actor':
            ilObjCmiXapi::log()->debug('tri par acteur');
                $column = 'utilisateur';
                ilUtil::sendInfo("Le tri par $column n'est pas disponible");
                break;
                
            case 'date':
            	ilObjCmiXapi::log()->debug('tri par date');
            	if ($this->filter->getOrderDirection()=='asc'){
            	    	$column='dateAsc';
            	    	}
            	else {$column='dateDesc';}
            	break;
            default:
            ilObjCmiXapi::log()->debug('tri par defaut');
                $column = 'dateDesc';
                break;
        }
        
        return $column;
    }

}
