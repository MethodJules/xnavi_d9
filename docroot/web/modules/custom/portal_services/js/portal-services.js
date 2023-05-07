/**
 * @file
 * x.Navi Portal Services behaviors.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Behavior description.
   */
  Drupal.behaviors.portalServices = {
    attach: function (context, settings) {

      console.log('It works!');

      

    }
  };

} (jQuery, Drupal));

goToPortal = () =>  {
  window.location.href = 'https://coderdojo-schoeneweide.github.io/';
}
