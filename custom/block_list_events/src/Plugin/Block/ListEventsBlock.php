<?php
namespace Drupal\block_list_events\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "block_list_events_example",
 *   admin_label = @Translation("List of events"),
 *   category = @Translation("block_list_events")
 * )
 */
class ListEventsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_node = \Drupal::routeMatch()->getParameter('node');

    // Vérifier si nous sommes sur une page de nœud et que le type est "event".
    if ($current_node instanceof Node && $current_node->getType() == 'event') {
      $taxonomy_term = $current_node->get('field_event_type')->entity;
      if ($taxonomy_term instanceof Term) {
        $events = $this->getEventsPublished($taxonomy_term->id(), $current_node->id());
        if (!empty($events)) {
          $build = [
            '#theme' => 'item_list',
            '#items' => [],
          ];

          foreach ($events as $event) {
            $markup = $event->toLink()->toString();
            $build['#items'][] = [
              '#markup' => $markup,
            ];
          }

          return $build;
        }
      }
    }

    return [
      '#markup' => $this->t('No related events found.'),
    ];
  }

  /**
   * Récupère les événements publiés.
   *
   * @param int $term_id
   *   L'ID du terme de taxonomie.
   * @param int $current_nid
   *   L'ID du nœud actuel pour exclusion.
   *
   * @return \Drupal\node\Entity\Node[]
   *   Les nœuds des événements.
   */
  protected function getEventsPublished($term_id, $current_nid) {
    $limit = 3;
    $current_time = DrupalDateTime::createFromTimestamp(time())->format('c');

    // Première requête pour récupérer les événements associés au terme.
    $query = \Drupal::entityQuery('node')
      ->accessCheck(TRUE)
      ->condition('status', 1)
      ->condition('type', 'event')
      ->condition('field_event_type.target_id', $term_id)
      ->condition('field_date_range.end_value', $current_time, '>=')
      ->condition('nid', $current_nid, '<>')
      ->sort('field_date_range', 'ASC')
      ->range(0, $limit);

    $nids = $query->execute();

    // Si moins de 3 événements, compléter avec d'autres événements non associés à ce terme.
    if (count($nids) < $limit) {
      $remaining = $limit - count($nids);

      $additional_query = \Drupal::entityQuery('node')
        ->accessCheck(TRUE)
        ->condition('status', 1)
        ->condition('type', 'event')
        ->condition('field_event_type.target_id', $term_id, '<>')
        ->condition('field_date_range.end_value', $current_time, '>=')
        ->condition('nid', $current_nid, '<>')
        ->sort('field_date_range', 'ASC')
        ->range(0, $remaining);

      $additional_nids = $additional_query->execute();
      $nids = array_merge($nids, $additional_nids);
    }

    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

}
