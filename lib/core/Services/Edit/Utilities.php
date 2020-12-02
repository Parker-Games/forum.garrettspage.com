<?php
// (c) Copyright by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

/**
 * Class Services_Utilities
 */
class Services_Edit_Utilities
{
  public function replacePlugin($input, $checkCsrf = true)
  {
    global $user;

    $tikilib = TikiLib::lib('tiki');

    $page = $input->page->pagename();
    $type = $input->type->word();
    $message = $input->message->text();
    $content = $input->content->wikicontent();
    $index = $input->index->int();
    $params = $input->asArray('params');
    $appendParams = $input->appendParams->int();

    $referer = $_SERVER['HTTP_REFERER'];

    if (! $page || ! $type || ! $referer) {
      throw new Services_Exception(tr('Missing parameters'));
    }

    $plugin = strtolower($type);

    if (! $message) {
      $message = tr('%0 Plugin modified by editor.', $plugin);
    }

    $info = $tikilib->get_page_info($page);
    if (! $info) {
      throw new Services_Exception_NotFound(tr('Page "%0" not found', $page));
    }

    $perms = $tikilib->get_perm_object($page, 'wiki page', $info, false);
    if ($perms['tiki_p_edit'] !== 'y') {
      throw new Services_Exception_Denied(tr('You do not have permission to edit "%0"', $page));
    }

    $current = $info['data'];

    $matches = WikiParser_PluginMatcher::match($current);
    $count = 0;
    $util = new Services_Utilities();
    foreach ($matches as $match) {
      if ($match->getName() !== $plugin) {
        continue;
      }

      ++$count;

      if ($index === $count && (!$checkCsrf || $util->checkCsrf())) {
        // by using content of "~same~", it will not replace the body that is there
        if ($content == "~same~") {
          $content = $match->getBody();
        }

        if (! $params) {
          $params = $match->getArguments();
        } elseif ($appendParams) {
          $parser = new WikiParser_PluginArgumentParser;
          $arguments = $parser->parse($match->getArguments());
          $params = array_merge($arguments, $params);
        }

        $match->replaceWithPlugin($plugin, $params, $content);

        $tikilib->update_page(
          $page,
          $matches->getText(),
          $message,
          $user,
          $tikilib->get_ip_address()
        );
        Feedback::success($message);
        return [];
      }
    }
    throw new Exception('Plugin edit failed');
  }
}
