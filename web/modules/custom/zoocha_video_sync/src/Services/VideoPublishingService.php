<?php

namespace Drupal\zoocha_video_sync\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\media\Entity\Media;
use Drupal\node\NodeInterface;

/**
 * Service class responsible for synchronizing the publication state of videos
 * with their parent node.
 */
class VideoPublishingService {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger channel for the module.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs a VideoPublishingService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger channel for the module.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerChannelInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
  }

  /**
   * Synchronizes the video state with its parent node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity being processed.
   */
  public function synchronizeVideoState(NodeInterface $node) {
    // Ensure the node is of type 'landing_page'.
    if ($node->bundle() === 'landing_page') {
      $components = $node->get('field_components')->referencedEntities();
      foreach ($components as $component) {
        // Check if the component has a field for remote videos.
        if ($component->hasField('field_remote_video')) {
          $videos = $component->get('field_remote_video')->referencedEntities();
          foreach ($videos as $video) {
            // Ensure the entity is a media and is of the correct bundle.
            if ($video instanceof Media && $video->bundle() === 'remote_video') {
              // If the node is unpublished, unpublish all videos.
              if (!$node->isPublished()) {
                $this->updateVideoPublishingStatus($video, FALSE);
              }
              else {
                // Otherwise, retain the video's current published status.
                $this->retainVideoPublishingStatus($video);
              }
            }
          }
        }
      }
    }
  }

  /**
   * Updates the publishing status of a video.
   *
   * @param \Drupal\media\Entity\Media $video
   *   The video entity.
   * @param bool $publish
   *   Whether to publish or unpublish the video.
   */
  private function updateVideoPublishingStatus(Media $video, $publish) {
    // Update the status field based on the publication state.
    $video->set('status', $publish ? 1 : 0);
    // Save the media entity with error handling.
    try {
      $video->save();
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to update video status for media ID ' . $video->id() . ': ' . $e->getMessage());
    }
  }

  /**
   * Retains the current publishing status of the video.
   *
   * @param \Drupal\media\Entity\Media $video
   *   The video entity.
   */
  private function retainVideoPublishingStatus(Media $video) {
    // This method is a placeholder if additional logic is needed in the future.
    // Currently, it is left empty as no action is required to retain the status.
  }
}
