<?php

namespace Ant\PhotoRestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AntPhotoRestExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
    	$processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        //$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
       // $loader->load('services.yml');
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form.yml');
        $loader->load('listeners.yml');
        //selecionamos el tipo de db_driver
        if (!in_array(strtolower($config['db_driver']), array('orm'))) {
        	throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.yml', $config['db_driver']));

        $container->setParameter('ant.photo_rest.model.participant_interface.class', $config['participant_class']);
        $container->setParameter('ant.photo_rest.model.photo.class', $config['photo_class']);
        $container->setParameter('ant.photo_rest.model.album.class', $config['album_class']);
        $container->setParameter('ant.photo_rest.model.vote.class', $config['vote_class']);

//         $container->setParameter('ant_badge.badge_class', $config['badge_class']);
//         $container->setParameter('ant_badge.rank_class', $config['rank_class']);
//         $container->setParameter('ant_badge.group_class', $config['group_class']);
        
//         $container->setParameter('ant_badge.new_badge_form.model', $config['new_badge_form']['model']);
//         $container->setParameter('ant_badge.new_badge_form.name', $config['new_badge_form']['name']);
        
//         $container->setParameter('ant_badge.new_group_form.name', $config['new_group_form']['name']);
//         $container->setParameter('ant_badge.new_group_form.model', $config['new_group_form']['model']);
    }
}
