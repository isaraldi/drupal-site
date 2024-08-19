<?php

declare(strict_types=1);

namespace Drupal\telemetry\Module;

trait TelemetryModule {

  /**
   * lines
   *
   * Breaks arrays to strings using delimiter character.
   *
   * @param  mixed  $lines    Array or string to be processed.
   * @param  string $breaker  Line breaker character.
   * @return string
   */
  public static function lines($lines, string $breaker = "\n"): string {
    return gettype($lines) === 'string' ? $lines : implode($breaker, $lines);
  }

  /**
   * telemetry
   *
   * Sends telemetry messages to the mothership.
   *
   * @param  mixed $type      Type of message to be sent.
   * @param  mixed $origin    Class origin of the message.
   * @param  mixed $message   Message payload.
   * @param  mixed $more_info More information to append to the message.
   * @return string Original message.
   */
  public static function telemetry($type, $origin = __CLASS__, string $message = '', $more_info = []): string {
    // $config = \Drupal::config('telemetry.settings');
    // die('<pre>wololo 2<br>'.print_r($config->get('slack.key'), true));
    $now = date('Y/m/d H:i:s');
    \Drupal::httpClient()
      ->post('https://hooks.slack.com/services/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', [
        'headers' => ['Content-type' => 'Content-type: application/json'],
        'body' => json_encode([
          "blocks" => [
              [
                "type" => "header",
                "text" => [
                  "type" => "plain_text",
                  "text" => gettype($origin) === 'string' ? $origin : get_class($origin),
                ]
              ],
              [
                "type" => "section",
                "text" => [
                  "type" => "plain_text",
                  "text" => self::lines([$message, self::lines($more_info)]),
                ]
              ],
              [
                "type" => "section",
                "fields" => [
                  [
                    "type" => "mrkdwn",
                    "text" => self::lines(["*Type:*", $type]),
                  ],
                  [
                    "type" => "mrkdwn",
                    "text" => self::lines(["*Machine user:*", getenv('USER')]),
                  ],
                ]
              ],
            ]
        ])
      ]);
    return $message;
  }
}
