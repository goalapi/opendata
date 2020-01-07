<?php
/**
 * GoalAPI - OpenData
 * Author: Murat Erkenov <murat@11bits.net>, 11bits, Ltd., Russia
 * Date: 3/13/17 8:19 PM
 *
 */

namespace App\AppBundle\Command;

use GoalAPI\SDKBundle\GoalAPISDK;
use GoalAPI\SDKBundle\Model;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DumpCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var GoalAPISDK
     */
    private $sdk;

    public function configure()
    {
        $this->setName('goalapi:dump');
        $this->addArgument(
            'tournaments',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'List of tournaments to dump'
        );
        $this->addOption('output_dir', 'o', InputOption::VALUE_OPTIONAL, 'Output directory');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $this->setupOutputPath($input, $output);

        /** @var GoalAPISDK $sdk */
        $sdk = $this->sdk;

        $tournamentsFromInput = $input->getArgument('tournaments');
        foreach ($this->getTournaments($sdk) as $tournament) {
            if (sizeof($tournamentsFromInput) && !in_array($tournament->getId(), $tournamentsFromInput)) {
                continue;
            }
            $sdk->getTournament($tournament->getId());
            foreach ($this->getSeasons($sdk, $tournament) as $season) {
                $sdk->getSeason($tournament, $season->getId());
                foreach ($this->getStages($sdk, $tournament, $season) as $stage) {
                    $this->processStage($sdk, $tournament, $season, $stage);
                    $output->writeln($stage->getId().'::'.$stage->getName());
                }
            }
        }

        return 0;
    }

    /**
     * @param GoalAPISDK $sdk
     */
    public function setSdk(GoalAPISDK $sdk): void
    {
        $this->sdk = $sdk;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setupOutputPath(InputInterface $input, OutputInterface $output): void
    {
        $dataPath = $input->getOption('output_dir');
        if ($dataPath) {
            if (!is_writable($dataPath) || !is_dir($dataPath)) {
                $output->writeln("Directory '$dataPath' not exists or not writable");
            }
            $dataPath = realpath($dataPath);

            $dataLoadListener = $this->container->get('app.goalapi_listener.load');
            $dataLoadListener->setDataPath($dataPath);
        }
    }

    /**
     * @param GoalAPISDK $sdk
     * @return \Generator | Model\Tournament[]
     */
    protected function getTournaments(GoalAPISDK $sdk)
    {
        foreach ($sdk->getTournaments() as $tournament) {
            yield $tournament;
        }
    }

    /**
     * @param GoalAPISDK $sdk
     * @param Model\Tournament $tournament
     * @return \Generator|Model\Season[]
     */
    protected function getSeasons(GoalAPISDK $sdk, Model\Tournament $tournament)
    {
        foreach ($sdk->getSeasons($tournament) as $season) {
            yield $season;
        }
    }

    /**
     * @param GoalAPISDK $sdk
     * @param Model\Tournament $tournament
     * @param Model\Season $season
     * @return \Generator|Model\Stage[]
     */
    protected function getStages($sdk, $tournament, $season)
    {
        foreach ($sdk->getStages($tournament, $season) as $stage) {
            yield $stage;
        }
    }

    /**
     * @param GoalAPISDK $sdk
     * @param Model\Tournament $tournament
     * @param Model\Season $season
     * @param Model\Stage $stage
     */
    protected function processStage(
        GoalAPISDK $sdk,
        Model\Tournament $tournament,
        Model\Season $season,
        Model\Stage $stage
    ) {
        $stageFromAPI = $sdk->getStage($tournament, $season, $stage->getId());
        $sdk->getMatches($tournament, $season, $stage);
        if ($stageFromAPI->hasStandings()) {
            $sdk->getStandings($tournament, $season, $stage);
        }
//        foreach ($this->getSquads($sdk, $tournament, $season, $stage) as $squad) {
//            $sdk->getSquad($tournament, $season, $stage, $squad->getTeam());
//        }
    }

    /**
     * @param GoalAPISDK $sdk
     * @param Model\Tournament $tournament
     * @param Model\Season $season
     * @param Model\Stage $stage
     * @return \Generator|Model\Squad[]
     */
    protected function getSquads(
        GoalAPISDK $sdk,
        Model\Tournament $tournament,
        Model\Season $season,
        Model\Stage $stage
    ) {
        foreach ($sdk->getSquads($tournament, $season, $stage) as $squad) {
            yield $squad;
        }
    }
}
