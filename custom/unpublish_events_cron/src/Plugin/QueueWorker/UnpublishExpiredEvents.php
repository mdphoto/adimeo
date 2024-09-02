<?php


namespace Drupal\unpublish_events_cron\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\Entity\Node;

/**
 * Unpublish expired events.
 *
 * @QueueWorker(
 *   id = "unpublish_expired_events",
 *   title = @Translation("Unpublish expired events"),
 *   cron = {"time" = 60}
 * )
 */
class UnpublishExpiredEvents extends QueueWorkerBase
{

  /**
   * {@inheritdoc}
   */
  public function processItem($data)
  {
    $nid = $data['nid'];
    $node = Node::load($nid);

    if ($node && $node->isPublished()) {
      $end_date = $node->get('field_date_range')->end_value;
      if (strtotime($end_date) < time()) {
        $node->setUnpublished();
        $node->save();
      }
    }
  }
}
