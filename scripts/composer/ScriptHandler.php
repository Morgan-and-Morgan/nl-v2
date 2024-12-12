<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Script handler class.
 */
class ScriptHandler {

  /**
   * Fetch the drupal root folder.
   *
   * @param string $project_root
   *   Project root directory.
   *
   * @return string
   *   The Drupal root.
   */
  protected static function getDrupalRoot($project_root) {
    return $project_root . '/web';
  }

  /**
   * Create required files.
   *
   * @param Composer\Script\Event $event
   *   Composer script event.
   */
  public static function createRequiredFiles(Event $event) {
    $fs = new Filesystem();
    $root = static::getDrupalRoot(getcwd());

    $dirs = [
      'modules',
      'profiles',
      'themes',
    ];

    // Required for unit testing.
    foreach ($dirs as $dir) {
      if (!$fs->exists($root . '/' . $dir)) {
        $fs->mkdir($root . '/' . $dir);
        $fs->touch($root . '/' . $dir . '/.gitkeep');
      }
    }

    // Create the files directory with chmod 0777.
    if (!$fs->exists($root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write("Create a sites/default/files directory with chmod 0777");
    }
  }

  /**
   * This is called by the QuickSilver deploy hook.
   *
   * Convert from a 'lean' repository to a 'fat' repository. This should only be
   * called when using this repository as a custom upstream, and
   * updating it with `terminus composer <site>.<env> update`. This
   * is not used in the GitHub PR workflow.
   */
  public static function prepareForPantheon() {
    // Get rid of any .git directories that Composer may have added.
    // n.b. Ideally, there are none of these, as removing them may
    // impair Composer's ability to update them later. However, leaving
    // them in place prevents us from pushing to Pantheon.
    $dirsToDelete = [];
    $finder = new Finder();
    foreach (
      $finder
        ->directories()
        ->in(getcwd())
        ->ignoreDotFiles(FALSE)
        ->ignoreVCS(FALSE)
        ->depth('> 0')
        ->name('.git') as $dir) {
      $dirsToDelete[] = $dir;
    }
    $fs = new Filesystem();
    $fs->remove($dirsToDelete);

    // Fix up .gitignore: remove everything above the "::: cut :::" line.
    $gitignoreFile = getcwd() . '/.gitignore';
    $gitignoreContents = file_get_contents($gitignoreFile);
    $gitignoreContents = preg_replace('/.*::: cut :::*/s', '', $gitignoreContents);
    file_put_contents($gitignoreFile, $gitignoreContents);

  }

}
