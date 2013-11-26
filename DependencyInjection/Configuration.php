<?php

namespace Ant\PhotoRestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ant_photo_rest')
	        ->children()
	        	->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()
        		->scalarNode('participant_class')->isRequired()->cannotBeEmpty()->end()
        		->scalarNode('photo_class')->isRequired()->cannotBeEmpty()->end()
        		->scalarNode('album_class')->isRequired()->cannotBeEmpty()->end()
        		->scalarNode('vote_class')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('upload')
                    ->children()
                        ->arrayNode('thumbnails')
                            ->isRequired()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('width')->defaultValue(false)->end()
                                    ->scalarNode('height')->defaultValue(false)->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        	;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
//         $rootNode
//         	->children()
//         		->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
//         		->scalarNode('badge_class')->isRequired()->cannotBeEmpty()->end()
//         		->scalarNode('rank_class')->isRequired()->cannotBeEmpty()->end()
//         		->scalarNode('group_class')->isRequired()->cannotBeEmpty()->end()
//         		->scalarNode('participant_provider')->defaultValue('fos_message.participant_provider.default')->cannotBeEmpty()->end()
        		
//         		->arrayNode('new_badge_form')
// 	        		->addDefaultsIfNotSet()
// 	        		->children()
// 	        			->scalarNode('name')->defaultValue('badge')->cannotBeEmpty()->end()
// 		        		->scalarNode('model')->defaultValue('Ant\BadgeBundle\FormModel\NewBadge')->end()
// 	        		->end()
//         		->end()
//         		->arrayNode('new_group_form')
// 	        		->addDefaultsIfNotSet()
// 	        		->children()
// 		        		->scalarNode('name')->defaultValue('group')->cannotBeEmpty()->end()
// 		        		->scalarNode('model')->defaultValue('Ant\BadgeBundle\FormModel\NewGroup')->end()
// 	        		->end()
//         		->end()
//         		;

        return $treeBuilder;
    }
}
