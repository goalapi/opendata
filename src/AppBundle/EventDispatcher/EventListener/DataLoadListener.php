<?php
/**
 * GoalAPI - OpenData
 * Author: Murat Erkenov <murat@11bits.net>, 11bits, Ltd., Russia
 * Date: 5/8/17 3:46 PM
 *
 */

namespace GoalAPI\OpenData\Bundle\AppBundle\EventDispatcher\EventListener;

use GoalAPI\SDKBundle\GoalAPISDK\CallPerformer;
use GoalAPI\SDKBundle\GoalAPISDK\EventDispatcher\Event\GoalAPISDKEvent;

class DataLoadListener
{

    /**
     * @var String
     */
    private $dataPath;

    /**
     * @param String $dataPath
     */
    public function setDataPath($dataPath)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @param GoalAPISDKEvent $event
     * @return null
     */
    public function onDataLoad(GoalAPISDKEvent $event)
    {
        $arguments = $event->getArguments();
        $result = $event->getResult();

        $path = null;

        switch ($event->getCallName()) {
            case 'GetTournaments':
                $path = 'tournaments.json';
                break;

            case 'GetTournament':
                $path = 'tournaments/'.$arguments[0].'.json';
                break;

            case 'GetSeasons':
                $ids = [
                    $arguments[0]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/seasons.json';
                break;

            case 'GetSeason':
                $ids = [
                    $arguments[0]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/seasons/'.$arguments[1].'.json';
                break;

            case 'GetStages':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/stages.json';
                break;

            case 'GetStage':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/stages/'.$arguments[2].'.json';
                break;

            case 'GetMatches':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                    $arguments[2]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/matches.json';
                break;

            case 'GetMatch':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                    $arguments[2]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/matches/'.$arguments[3].'.json';
                break;

            case 'GetStandings':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                    $arguments[2]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/standings.json';
                break;

            case 'GetSquads':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                    $arguments[2]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/teams.json';
                break;

            case 'GetSquad':
                $ids = [
                    $arguments[0]->getId(),
                    $arguments[1]->getId(),
                    $arguments[2]->getId(),
                ];
                $path = CallPerformer::pathFromIds($ids).'/teams/'.$arguments[3]->getId().'.json';
                break;
        }

        if ($path) {
            $this->writeResult($path, $result);
        }
    }

    protected function writeResult($path, $result)
    {
        $filePath = $this->dataPath.'/'.$path;
        $dirName = dirname($filePath);

        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        file_put_contents($filePath, $result);
    }
}
