<?php
/**
 * Seed navigation — primary and footer menus for Pacífica Panadería.
 *
 * Returns the navigation structures consumed by
 * {@see \Pacifica\Core\Setup\Installer::install_navigation()} to build a
 * `wp_navigation` post (block themes) and, when a classic theme is active, a
 * `wp_nav_menu` assigned to the header location.
 *
 * Item fields:
 *   label string  Visible label (Spanish).
 *   slug  string  Page slug to resolve to a permalink at install time. Mutually
 *                 exclusive with `url`.
 *   url   string  Absolute or root-relative URL (used when there is no page,
 *                 e.g. the shop/menu "Ordenar" call to action).
 *   cta   bool    (optional) Marks a button-styled call-to-action item.
 *
 * Slugs are resolved to live permalinks by the installer; if a page is missing,
 * the item is skipped so the menu never links to a 404.
 *
 * @package Pacifica\Core
 * @return array<string,array<int,array<string,mixed>>>
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	// Primary navigation — header. Mirrors the IA in the brand brief §4.
	'primary' => array(
		array( 'label' => 'Inicio',    'slug' => 'inicio' ),
		array( 'label' => 'Menú',      'slug' => 'menu' ),
		array( 'label' => 'Historia',  'slug' => 'nuestra-historia' ),
		array( 'label' => 'Proceso',   'slug' => 'nuestro-proceso' ),
		array( 'label' => 'Catering',  'slug' => 'catering' ),
		array( 'label' => 'Contacto',  'slug' => 'contacto' ),
		array( 'label' => 'Ordenar',   'url' => '/menu', 'cta' => true ),
	),

	// Footer navigation — utility and legal links.
	'footer' => array(
		array( 'label' => 'Cómo Recoger',          'slug' => 'como-recoger' ),
		array( 'label' => 'Preguntas Frecuentes',  'slug' => 'preguntas-frecuentes' ),
		array( 'label' => 'Aviso de Privacidad',   'slug' => 'aviso-de-privacidad' ),
		array( 'label' => 'Términos',              'slug' => 'terminos-y-condiciones' ),
		array( 'label' => 'Reembolsos',            'slug' => 'politica-de-reembolsos' ),
	),

);
