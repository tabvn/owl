<?php

namespace Drupal\owlcarousel\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'owl_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "owlcarousel_field_formatter",
 *   label = @Translation("OwlCarousel Carousel"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class OwlCarouselFieldFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  protected $currentUser;
  protected $imageStyleStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, EntityStorageInterface $image_style_storage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->currentUser = $current_user;
    $this->imageStyleStorage = $image_style_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('entity.manager')->getStorage('image_style')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return _owlcarousel_default_settings() + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $image_styles = image_style_options(FALSE);
    $description_link = Link::fromTextAndUrl(
      $this->t('Configure Image Styles'),
      Url::fromRoute('entity.image_style.collection')
    );
    $element['image_style'] = [
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => $this->t('None (original image)'),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable() + [
        '#access' => $this->currentUser->hasPermission('administer image styles'),
      ],
    ];
    $link_types = [
      'content' => $this->t('Content'),
      'file' => $this->t('File'),
    ];
    $element['image_link'] = [
      '#title' => $this->t('Link image to'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_link'),
      '#empty_option' => $this->t('Nothing'),
      '#options' => $link_types,
    ];

    $element['items'] = [
      '#type' => 'number',
      '#title' => $this->t('Items'),
      '#description' => $this->t('Maximum amount of items displayed at a time with the widest browser width.'),
      '#default_value' => $this->getSetting('items'),
    ];
    $element['itemsDesktop'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Desktop'),
      '#description' => $this->t('This allows you to preset the number of slides visible with a particular browser width. The format is [x,y] whereby x=browser width and y=number of slides displayed. For example [1199,4] means that if(window<=1199){ show 4 slides per page}'),
      '#default_value' => $this->getSetting('itemsDesktop'),
    ];
    $element['itemsDesktopSmall'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Desktop Small'),
      '#description' => $this->t('Example: [979,3]'),
      '#default_value' => $this->getSetting('itemsDesktopSmall'),
    ];
    $element['itemsTablet'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Tablet'),
      '#description' => $this->t('Example: [768,2]'),
      '#default_value' => $this->getSetting('itemsTablet'),
    ];
    $element['itemsMobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Items Mobile'),
      '#description' => $this->t('Example: [479,1]'),
      '#default_value' => $this->getSetting('itemsMobile'),
    ];
    $element['singleItem'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Single Item'),
      '#default_value' => $this->getSetting('singleItem'),
      '#description' => $this->t('Display only one item.'),
    ];
    // itemsScaleUp.
    $element['itemsScaleUp'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Items ScaleUp'),
      '#default_value' => $this->getSetting('itemsScaleUp'),
      '#description' => $this->t('Option to not stretch items when it is less than the supplied items.'),
    ];
    // slideSpeed.
    $element['slideSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Slide Speed'),
      '#default_value' => $this->getSetting('slideSpeed'),
      '#description' => $this->t('Slide speed in milliseconds.'),
    ];
    // paginationSpeed.
    $element['paginationSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Pagination Speed'),
      '#default_value' => $this->getSetting('paginationSpeed'),
      '#description' => $this->t('Pagination speed in milliseconds.'),
    ];
    // rewindSpeed.
    $element['rewindSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Rewind Speed'),
      '#default_value' => $this->getSetting('rewindSpeed'),
      '#description' => $this->t('Rewind speed in milliseconds.'),
    ];
    // autoPlay.
    $element['autoPlay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('AutoPlay'),
      '#default_value' => $this->getSetting('autoPlay'),
    ];
    // stopOnHover.
    $element['stopOnHover'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Stop On Hover'),
      '#default_value' => $this->getSetting('stopOnHover'),
      '#description' => $this->t('Stop autoplay on mouse hover.'),
    ];
    // Navigation.
    $element['navigation'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Navigation'),
      '#default_value' => $this->getSetting('navigation'),
      '#description' => $this->t('Display "next" and "prev" buttons.'),
    ];
    // prevText.
    $element['prevText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prev Text'),
      '#default_value' => $this->getSetting('prevText'),
      '#description' => $this->t('Text for navigation prev button'),
    ];
    // nextText.
    $element['nextText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Text'),
      '#default_value' => $this->getSetting('nextText'),
      '#description' => $this->t('Text for navigation next button'),
    ];
    // rewindNav.
    $element['rewindNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Rewind Nav'),
      '#default_value' => $this->getSetting('rewindNav'),
      '#description' => $this->t('Slide to first item.'),
    ];
    // scrollPerPage.
    $element['scrollPerPage'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Scroll Per Page'),
      '#default_value' => $this->getSetting('scrollPerPage'),
      '#description' => $this->t('Scroll per page not per item. This affect next/prev buttons and mouse/touch dragging.'),
    ];
    // Pagination.
    $element['pagination'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('pagination'),
      '#default_value' => $this->getSetting('pagination'),
      '#description' => $this->t('Show pagination.'),
    ];
    // paginationNumbers.
    $element['paginationNumbers'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pagination Numbers'),
      '#default_value' => $this->getSetting('paginationNumbers'),
      '#description' => $this->t('Show numbers inside pagination buttons.'),
    ];
    // Responsive.
    $element['responsive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Responsive'),
      '#default_value' => $this->getSetting('responsive'),
      '#description' => $this->t('Uncheck to use OwlCarousel Carousel on desktop-only.'),
    ];
    // responsiveRefreshRate.
    $element['responsiveRefreshRate'] = [
      '#type' => 'number',
      '#title' => $this->t('Responsive Refresh Rate'),
      '#default_value' => $this->getSetting('responsiveRefreshRate'),
      '#description' => $this->t('Check window width changes every 200ms for responsive actions.'),
    ];
    // mouseDrag.
    $element['mouseDrag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Mouse Drag'),
      '#default_value' => $this->getSetting('mouseDrag'),
      '#description' => $this->t('Turn off/on mouse events.'),
    ];
    // touchDrag.
    $element['touchDrag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Touch Drag'),
      '#default_value' => $this->getSetting('touchDrag'),
      '#description' => $this->t('Turn off/on touch events.'),
    ];
    // transitionStyle.
    $element['transitionStyle'] = [
      '#type' => 'select',
      '#options' => [
        'fade' => $this->t('Fade'),
        'backSlide' => $this->t('Back Slide'),
        'goDown' => $this->t('Go Down'),
        'scaleUp' => $this->t('ScaleUp'),
      ],
      '#title' => $this->t('Transition Style'),
      '#default_value' => $this->getSetting('transitionStyle'),
      '#description' => $this->t('Add CSS3 transition style. Works only with one item on screen.'),
    ];
    return $element + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // TODO: Implement settings summary.
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = [];

    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    $url = NULL;
    $image_link_setting = $this->getSetting('image_link');
    // Check if the formatter involves a link.
    if ($image_link_setting == 'content') {
      $entity = $items->getEntity();
      if (!$entity->isNew()) {
        $url = $entity->urlInfo();
      }
    }
    elseif ($image_link_setting == 'file') {
      $link_file = TRUE;
    }

    $image_style_setting = $this->getSetting('image_style');

    // Collect cache tags to be added for each item in the field.
    $cache_tags = [];
    if (!empty($image_style_setting)) {
      $image_style = $this->imageStyleStorage->load($image_style_setting);
      $cache_tags = $image_style->getCacheTags();
    }

    foreach ($files as $delta => $file) {
      if (isset($link_file)) {
        $image_uri = $file->getFileUri();
        $url = Url::fromUri(file_create_url($image_uri));
      }
      $cache_tags = Cache::mergeTags($cache_tags, $file->getCacheTags());

      // Extract field item attributes for the theme function, and unset them
      // from the $item so that the field template does not re-render them.
      $item = $file->_referringItem;
      $item_attributes = $item->_attributes;
      unset($item->_attributes);

      $elements[$delta] = [
        '#theme' => 'image_formatter',
        '#item' => $item,
        '#item_attributes' => $item_attributes,
        '#image_style' => $image_style_setting,
        '#url' => $url,
        '#cache' => [
          'tags' => $cache_tags,
        ],
      ];
    }

    $settings = _owlcarousel_default_settings();
    foreach ($settings as $k => $v) {
      $s = $this->getSetting($k);
      $settings[$k] = isset($s) ? $s : $settings[$k];
    }
    return [
      '#theme' => 'owl',
      '#items' => $elements,
      '#settings' => $settings,
      '#attached' => ['library' => ['owlcarousel/owlcarousel']],
    ];

  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
