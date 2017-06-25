<?php

namespace Drupal\owlcarousel\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render each item into owl carousel.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "owlcarousel",
 *   title = @Translation("OwlCarousel"),
 *   help = @Translation("Displays rows as OwlCarousel."),
 *   theme = "owlcarousel_views",
 *   display_types = {"normal"}
 * )
 */
class OwlCarousel extends StylePluginBase {

  /**
   * Does the style plugin allows to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Set default options.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $settings = _owlcarousel_default_settings();
    foreach ($settings as $k => $v) {
      $options[$k] = ['default' => $v];
    }
    return $options;
  }

  /**
   * Render the given style.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['items'] = [
      '#type' => 'number',
      '#title' => $this->t('Items'),
      '#description' => $this->t('Maximum amount of items displayed at a time with the widest browser width.'),
      '#default_value' => $this->options['items'],
    ];
    $form['itemsDesktop'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Desktop'),
      '#description' => $this->t('This allows you to preset the number of slides visible with a particular browser width. The format is [x,y] whereby x=browser width and y=number of slides displayed. For example [1199,4] means that if(window<=1199){ show 4 slides per page}'),
      '#default_value' => $this->options['itemsDesktop'],
    ];
    $form['itemsDesktopSmall'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Desktop Small'),
      '#description' => $this->t('Example: [979,3]'),
      '#default_value' => $this->options['itemsDesktopSmall'],
    ];
    $form['itemsTablet'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Tablet'),
      '#description' => $this->t('Example: [768,2]'),
      '#default_value' => $this->options['itemsTablet'],
    ];
    $form['itemsMobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Mobile'),
      '#description' => $this->t('Example: [479,1]'),
      '#default_value' => $this->options['itemsMobile'],
    ];
    $form['singleItem'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Single Item'),
      '#default_value' => $this->options['singleItem'],
      '#description' => $this->t('Display only one item.'),
    ];
    // itemsScaleUp.
    $form['itemsScaleUp'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Items ScaleUp'),
      '#default_value' => $this->options['itemsScaleUp'],
      '#description' => $this->t('Option to not stretch items when it is less than the supplied items.'),
    ];
    // slideSpeed.
    $form['slideSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Slide Speed'),
      '#default_value' => $this->options['slideSpeed'],
      '#description' => $this->t('Slide speed in milliseconds.'),
    ];
    // paginationSpeed.
    $form['paginationSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Pagination Speed'),
      '#default_value' => $this->options['paginationSpeed'],
      '#description' => $this->t('Pagination speed in milliseconds.'),
    ];
    // rewindSpeed.
    $form['rewindSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Rewind Speed'),
      '#default_value' => $this->options['rewindSpeed'],
      '#description' => $this->t('Rewind speed in milliseconds.'),
    ];
    // autoPlay.
    $form['autoPlay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('AutoPlay'),
      '#default_value' => $this->options['autoPlay'],
    ];
    // stopOnHover.
    $form['stopOnHover'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Stop On Hover'),
      '#default_value' => $this->options['stopOnHover'],
      '#description' => $this->t('Stop autoplay on mouse hover.'),
    ];
    // Navigation.
    $form['navigation'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Navigation'),
      '#default_value' => $this->options['navigation'],
      '#description' => $this->t('Display "next" and "prev" buttons.'),
    ];
    // prevText.
    $form['prevText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prev Text'),
      '#default_value' => $this->options['prevText'],
      '#description' => $this->t('Text for navigation prev button'),
    ];
    // nextText.
    $form['nextText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Text'),
      '#default_value' => $this->options['nextText'],
      '#description' => $this->t('Text for navigation next button'),
    ];
    // rewindNav.
    $form['rewindNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Rewind Nav'),
      '#default_value' => $this->options['rewindNav'],
      '#description' => $this->t('Slide to first item.'),
    ];
    // scrollPerPage.
    $form['scrollPerPage'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Scroll Per Page'),
      '#default_value' => $this->options['scrollPerPage'],
      '#description' => $this->t('Scroll per page not per item. This affect next/prev buttons and mouse/touch dragging.'),
    ];
    // Pagination.
    $form['pagination'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('pagination'),
      '#default_value' => $this->options['pagination'],
      '#description' => $this->t('Show pagination.'),
    ];
    // paginationNumbers.
    $form['paginationNumbers'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pagination Numbers'),
      '#default_value' => $this->options['paginationNumbers'],
      '#description' => $this->t('Show numbers inside pagination buttons.'),
    ];
    // Responsive.
    $form['responsive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Responsive'),
      '#default_value' => $this->options['responsive'],
      '#description' => $this->t('Uncheck to use OwlCarousel Carousel on desktop-only.'),
    ];
    // responsiveRefreshRate.
    $form['responsiveRefreshRate'] = [
      '#type' => 'number',
      '#title' => $this->t('Responsive Refresh Rate'),
      '#default_value' => $this->options['responsiveRefreshRate'],
      '#description' => $this->t('Check window width changes every 200ms for responsive actions.'),
    ];
    // mouseDrag.
    $form['mouseDrag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Mouse Drag'),
      '#default_value' => $this->options['mouseDrag'],
      '#description' => $this->t('Turn off/on mouse events.'),
    ];
    // touchDrag.
    $form['touchDrag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Touch Drag'),
      '#default_value' => $this->options['touchDrag'],
      '#description' => $this->t('Turn off/on touch events.'),
    ];
    // transitionStyle.
    $form['transitionStyle'] = [
      '#type' => 'select',
      '#options' => [
        'fade' => $this->t('Fade'),
        'backSlide' => $this->t('Back Slide'),
        'goDown' => $this->t('Go Down'),
        'scaleUp' => $this->t('ScaleUp'),
      ],
      '#title' => $this->t('Transition Style'),
      '#default_value' => $this->options['transitionStyle'],
      '#description' => $this->t('Add CSS3 transition style. Works only with one item on screen.'),
    ];

  }

}
