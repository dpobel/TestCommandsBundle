<?php

namespace DP\TestCommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRichTextMultipleClassesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'dp:createrichtextmultipleclasses' )
            ->setDescription( 'Create RichText with several classes on an element' );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $contentTypeService = $repository->getContentTypeService();

        $repository->setCurrentUser( $repository->getUserService()->loadUser( 14 ) );

        // fetch the input arguments
        $parentLocationId = 2;
        $contentTypeIdentifier = 'sarticle';
        $name = "Mutiple classes";
        $summary = '<?xml version="1.0" ?><section xmlns="http://ez.no/namespaces/ezpublish5/xhtml5/edit"><p class="class1 class2">Foo</p></section>';

        try
        {
            $contentType = $contentTypeService->loadContentTypeByIdentifier( $contentTypeIdentifier );
            $contentCreateStruct = $contentService->newContentCreateStruct( $contentType, 'eng-GB' );
            $contentCreateStruct->setField( 'title', $name );
            $contentCreateStruct->setField( 'body', $summary );

            // instantiate a location create struct from the parent location
            $locationCreateStruct = $locationService->newLocationCreateStruct( $parentLocationId );

            // create a draft using the content and location create struct and publish it
            $draft = $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );
            $content = $contentService->publishVersion( $draft->versionInfo );

            // print out the content
            print_r( $content );
        }
        // Content type or location not found
        catch ( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            $output->writeln( $e->getMessage() );
        }
        // Invalid field value
        catch ( \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $e )
        {
            $output->writeln( $e->getMessage() );
        }
        // Required field missing or empty
        catch ( \eZ\Publish\API\Repository\Exceptions\ContentValidationException $e )
        {
            $output->writeln( $e->getMessage() );
        }
    }
}
