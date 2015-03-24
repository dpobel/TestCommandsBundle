<?php

namespace DP\TestCommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class LocationSearchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'dp:locationsearch' )
            ->setDescription( 'Test location search' )
            ->addArgument(
                'locationId', InputArgument::REQUIRED,
                'The base location id'
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $location = $this->getContainer()->get( 'ezpublish.api.service.location' )->loadLocation(
            $input->getArgument( 'locationId' )
        );
        $output->writeLn( "Searching for the subitems of location '{$location->id}'" );

        $query = new LocationQuery();
        $query->filter = new Criterion\ParentLocationId( $location->id );
        $query->sortClauses[] = new SortClause\Location\Priority(
            LocationQuery::SORT_DESC
        );

        $result = $this->getContainer()->get( 'ezpublish.api.service.search' )->findLocations( $query );

        $output->writeLn( $result->totalCount . " results:" );
        foreach ( $result->searchHits as $hit )
        {
            $output->writeLn( "  * " . $hit->valueObject->contentInfo->name );
        }
    }
}
