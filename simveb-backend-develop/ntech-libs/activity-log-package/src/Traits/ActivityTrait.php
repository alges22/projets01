<?php
namespace Ntech\ActivityLogPackage\Traits;

use Spatie\Activitylog\Contracts\Activity;

trait ActivityTrait
{
    /***
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName): void
    {
        switch ($eventName)
        {
            case "created" :
                $action = $this->getEntityName()." créé";
                $actionName = 'Création';
                break;
            case "updated" :
                $action = $this->getEntityName()." mis à jour";
                $actionName = "Modification";
                break;
            case "deleted" :
                $action = $this->getEntityName()." supprimé";
                $actionName = "Suppression";
                break;
            default :
                $action = "Une opération est effectué sur un ".$this->getEntityName();
                $actionName = "Default";
                break;
        }

        $activity->ip_address = request()->ip();
        $activity->description = $action;
        $activity->log_action = $actionName;
    }


}
