<?php
/**
 * This file generates the main menu for the header on the back-end
 * and also for the default template.
 *
 * @package ProjectSend
 */
$items = [];

/**
 * Items for system users
 */
if (current_role_in([9, 8, 7])) {
  /** Count inactive CLIENTS */
  /*
	$sql_inactive = $dbh->prepare( "SELECT DISTINCT user FROM " . TABLE_USERS . " WHERE active = '0' AND level = '0' AND account_requested='0'" );
	$sql_inactive->execute();
	define('COUNT_CLIENTS_INACTIVE', $sql_inactive->rowCount());
	*/

  /**
   * Count new groups MEMBERSHIP requests only from active clients
   * to make sure they are from not new accounts (since those are
   * shown in the Account Request menu item)
   */
  define("COUNT_MEMBERSHIP_REQUESTS", countGroupsRequestsForExistingClients());

  /** Count ALREADY DENIED groups MEMBERSHIP requests */
  $sql_requests = $dbh->prepare(
    "SELECT DISTINCT id FROM " . TABLE_MEMBERS_REQUESTS . " WHERE denied='1'"
  );
  $sql_requests->execute();
  define("COUNT_MEMBERSHIP_DENIED", $sql_requests->rowCount());

  /** Count new CLIENTS account requests */
  $sql_requests = $dbh->prepare(
    "SELECT DISTINCT user FROM " .
      TABLE_USERS .
      " WHERE account_requested='1' AND account_denied='0'"
  );
  $sql_requests->execute();
  define("COUNT_CLIENTS_REQUESTS", $sql_requests->rowCount());

  /**
   * Count ALREADY DENIED account requests
   * Used on the manage requests page
   */
  $sql_requests = $dbh->prepare(
    "SELECT DISTINCT user FROM " .
      TABLE_USERS .
      " WHERE account_requested='1' AND account_denied='1'"
  );
  $sql_requests->execute();
  define("COUNT_CLIENTS_DENIED", $sql_requests->rowCount());

  /** Count inactive USERS */
  /*
	$sql_inactive = $dbh->prepare( "SELECT DISTINCT user FROM " . TABLE_USERS . " WHERE active = '0' AND level != '0'" );
	$sql_inactive->execute();
	define('COUNT_USERS_INACTIVE', $sql_inactive->rowCount());
	*/

  $items["dashboard"] = [
    "nav" => "dashboard",
    "level" => [9, 8, 7],
    "main" => [
      "label" => __("Dashboard", "cftp_admin"),
      "icon" => "tachometer",
      "link" => "dashboard.php",
    ],
  ];

  $items[] = "separator";

  $items["files"] = [
    "nav" => "files",
    "level" => [9, 8, 7],
    "main" => [
      "label" => __("Files", "cftp_admin"),
      "icon" => "file",
    ],
    "sub" => [
      [
        "label" => __("Upload", "cftp_admin"),
        "link" => "upload.php",
      ],
      [
        "divider" => true,
      ],
      [
        "label" => __("Manage files", "cftp_admin"),
        "link" => "manage-files.php",
      ],
      [
        "label" => __("Find orphan files", "cftp_admin"),
        "link" => "import-orphans.php",
      ],
      [
        "divider" => true,
      ],
      [
        "label" => __("Categories", "cftp_admin"),
        "link" => "categories.php",
      ],
    ],
  ];

  $items["clients"] = [
    "nav" => "clients",
    "level" => [9, 8],
    "main" => [
      "label" => __("Clients", "cftp_admin"),
      "icon" => "address-card",
      "badge" => COUNT_CLIENTS_REQUESTS,
    ],
    "sub" => [
      [
        "label" => __("Add new", "cftp_admin"),
        "link" => "clients-add.php",
      ],
      [
        "label" => __("Manage clients", "cftp_admin"),
        "link" => "clients.php",
        //'badge'	=> COUNT_CLIENTS_INACTIVE,
      ],
      [
        "divider" => true,
      ],
      [
        "label" => __("Account requests", "cftp_admin"),
        "link" => "clients-requests.php",
        "badge" => COUNT_CLIENTS_REQUESTS,
      ],
    ],
  ];

  $items["groups"] = [
    "nav" => "groups",
    "level" => [9, 8],
    "main" => [
      "label" => __("Clients Groups", "cftp_admin"),
      "icon" => "th-large",
      "badge" => COUNT_MEMBERSHIP_REQUESTS,
    ],
    "sub" => [
      [
        "label" => __("Add new", "cftp_admin"),
        "link" => "groups-add.php",
      ],
      [
        "label" => __("Manage groups", "cftp_admin"),
        "link" => "groups.php",
      ],
      [
        "divider" => true,
      ],
      [
        "label" => __("Membership requests", "cftp_admin"),
        "link" => "clients-membership-requests.php",
        "badge" => COUNT_MEMBERSHIP_REQUESTS,
      ],
    ],
  ];

  $items["users"] = [
    "nav" => "users",
    "level" => [9],
    "main" => [
      "label" => __("System Users", "cftp_admin"),
      "icon" => "users",
    ],
    "sub" => [
      [
        "label" => __("Add new", "cftp_admin"),
        "link" => "users-add.php",
      ],
      [
        "label" => __("Manage system users", "cftp_admin"),
        "link" => "users.php",
        //'badge'	=> COUNT_USERS_INACTIVE,
      ],
    ],
  ];

  $items[] = "separator";

  // $items["templates"] = [
  //   "nav" => "templates",
  //   "level" => [9],
  //   "main" => [
  //     "label" => __("Templates", "cftp_admin"),
  //     "icon" => "desktop",
  //   ],
  //   "sub" => [
  //     [
  //       "label" => __("Templates", "cftp_admin"),
  //       "link" => "templates.php",
  //     ],
  //   ],
  // ];

  $items["options"] = [
    "nav" => "options",
    "level" => [9],
    "main" => [
      "label" => __("Options", "cftp_admin"),
      "icon" => "cog",
    ],
    "sub" => [
      [
        "label" => __("General options", "cftp_admin"),
        "link" => "options.php?section=general",
      ],
      [
        "label" => __("Clients", "cftp_admin"),
        "link" => "options.php?section=clients",
      ],
      [
        "label" => __("Privacy", "cftp_admin"),
        "link" => "options.php?section=privacy",
      ],
      [
        "label" => __("E-mail notifications", "cftp_admin"),
        "link" => "options.php?section=email",
      ],
      [
        "label" => __("Security", "cftp_admin"),
        "link" => "options.php?section=security",
      ],
      [
        "label" => __("Branding", "cftp_admin"),
        "link" => "options.php?section=branding",
      ],
      [
        "label" => __("External Login", "cftp_admin"),
        "link" => "options.php?section=external_login",
      ],
      [
        "label" => __("Scheduled tasks (cron)", "cftp_admin"),
        "link" => "options.php?section=cron",
      ],
    ],
  ];

  $items["emails"] = [
    "nav" => "emails",
    "level" => [9],
    "main" => [
      "label" => __("E-mail Templates", "cftp_admin"),
      "icon" => "envelope",
    ],
    "sub" => [
      [
        "label" => __("Header / footer", "cftp_admin"),
        "link" => "email-templates.php?section=header_footer",
      ],
      [
        "label" => __("New file by user", "cftp_admin"),
        "link" => "email-templates.php?section=new_files_by_user",
      ],
      [
        "label" => __("New file by client", "cftp_admin"),
        "link" => "email-templates.php?section=new_files_by_client",
      ],
      [
        "label" => __("New client (welcome)", "cftp_admin"),
        "link" => "email-templates.php?section=new_client",
      ],
      [
        "label" => __("New client (self-registered)", "cftp_admin"),
        "link" => "email-templates.php?section=new_client_self",
      ],
      [
        "label" => __("Approve client account", "cftp_admin"),
        "link" => "email-templates.php?section=account_approve",
      ],
      [
        "label" => __("Deny client account", "cftp_admin"),
        "link" => "email-templates.php?section=account_deny",
      ],
      [
        "label" => __("Client updated memberships", "cftp_admin"),
        "link" => "email-templates.php?section=client_edited",
      ],
      [
        "label" => __("New user (welcome)", "cftp_admin"),
        "link" => "email-templates.php?section=new_user",
      ],
      [
        "label" => __("Password reset", "cftp_admin"),
        "link" => "email-templates.php?section=password_reset",
      ],
    ],
  ];

  $items[] = "separator";

  $items["tools"] = [
    "nav" => "tools",
    "level" => [9],
    "main" => [
      "label" => __("Tools", "cftp_admin"),
      "icon" => "wrench",
    ],
    "sub" => [
      [
        "label" => __("Actions log", "cftp_admin"),
        "link" => "actions-log.php",
      ],
      [
        "label" => __("Cron log", "cftp_admin"),
        "link" => "cron-log.php",
      ],
      [
        "label" => __("Test email configuration", "cftp_admin"),
        "link" => "email-test.php",
      ],
      [
        "label" => __("Unblock IP", "cftp_admin"),
        "link" => "unblock-ip.php",
      ],
    ],
  ];
} /**
 * Items for clients
 */ else {
  if (get_option("clients_can_upload") == 1) {
    $items["upload"] = [
      "nav" => "upload",
      "level" => [9, 8, 7, 0],
      "main" => [
        "label" => __("Upload", "cftp_admin"),
        "link" => "upload.php",
        "icon" => "cloud-upload",
      ],
    ];
  }

  $items["manage_files"] = [
    "nav" => "manage",
    "level" => [9, 8, 7, 0],
    "main" => [
      "label" => __("Edit Presets", "cftp_admin"),
      "link" => "manage-files.php",
      "icon" => "file",
    ],
  ];

  $items["view_files"] = [
    "nav" => "template",
    "level" => [9, 8, 7, 0],
    "main" => [
      "label" => __("View All Presets", "cftp_admin"),
      "link" => CLIENT_VIEW_FILE_LIST_URL_PATH,
      "icon" => "th-list",
    ],
  ];

  $items["ir"] = [
    "nav" => "ir",
    "level" => [9, 8, 7, 0],
    "main" => [
      "label" => __("IR Zip (10/2022)", "cftp_admin"),
      "link" => "/assets/Free_IR_Collection.zip",
      "icon" => "microphone",
    ],
  ];

  $items["link_tree"] = [
    "nav" => "link_tree",
    "level" => [9, 8, 7, 0],
    "main" => [
      "label" => __("External Links", "cftp_admin"),
      "link" => "link_tree.php",
      "icon" => "external-link-square",
    ],
  ];

  // $items["support"] = [
  //   "nav" => "support",
  //   "level" => [9, 8, 7, 0],
  //   "main" => [
  //     "label" => __("Support/Help", "cftp_admin"),
  //     "link" => "https://discord.gg/DX73QswUUg",
  //     "icon" => "question-circle",
  //   ],
  // ];

  $items["beer"] = [
    "nav" => "beer",
    "level" => [9, 8, 7, 0],
    "main" => [
      "label" => __("Buy Me A Beer", "cftp_admin"),
      "link" => "donate.php",
      "icon" => "beer",
    ],
  ];
}

/**
 * Build the menu
 */
$current_filename = parse_url(basename($_SERVER["REQUEST_URI"]));
$menu_output = "
    <div class='main_side_menu'>
        <ul class='main_menu' role='menu'>\n";

foreach ($items as $item) {
  if (!is_array($item) && $item == "separator") {
    $menu_output .= '<li class="separator"></li>';
    continue;
  }

  if (current_role_in($item["level"])) {
    $current =
      !empty($active_nav) && $active_nav == $item["nav"] ? "current_nav" : "";
    $badge = !empty($item["main"]["badge"])
      ? ' <span class="badge">' . $item["main"]["badge"] . "</span>"
      : "";
    $icon = !empty($item["main"]["icon"])
      ? '<i class="fa fa-' .
        $item["main"]["icon"] .
        ' fa-fw" aria-hidden="true"></i>'
      : "";

    /** Top level tag */
    if (!isset($item["sub"])) {
      $format =
        "<li class='%s'>\n\t<a href='%s' class='nav_top_level'>%s<span class='menu_label'>%s%s</span></a>\n</li>\n";
      $menu_output .= sprintf(
        $format,
        $current,
        BASE_URI . $item["main"]["link"],
        $icon,
        $badge,
        $item["main"]["label"]
      );
    } else {
      $first_child = $item["sub"][0];
      $top_level_link = !empty($first_child) ? $first_child["link"] : "#";
      $format =
        "<li class='has_dropdown %s'>\n\t<a href='%s' class='nav_top_level'>%s<span class='menu_label'>%s%s</span></a>\n\t<ul class='dropdown_content'>\n";
      $menu_output .= sprintf(
        $format,
        $current,
        $top_level_link,
        $icon,
        $item["main"]["label"],
        $badge
      );
      /**
       * Submenu
       */
      foreach ($item["sub"] as $subitem) {
        $badge = !empty($subitem["badge"])
          ? ' <span class="badge">' . $subitem["badge"] . "</span>"
          : "";
        $icon = !empty($subitem["icon"])
          ? '<i class="fa fa-' .
            $subitem["icon"] .
            ' fa-fw" aria-hidden="true"></i>'
          : "";
        if (!empty($subitem["divider"])) {
          $menu_output .= "\t\t<li class='divider'></li>\n";
        } else {
          $sub_active =
            $subitem["link"] == $current_filename["path"] ? "current_page" : "";
          $format =
            "\t\t<li class='%s'>\n\t\t\t<a href='%s'>%s<span class='submenu_label'>%s%s</span></a>\n\t\t</li>\n";
          $menu_output .= sprintf(
            $format,
            $sub_active,
            BASE_URI . $subitem["link"],
            $icon,
            $subitem["label"],
            $badge
          );
        }
      }
      $menu_output .= "\t</ul>\n</li>\n";
    }
  }
}

$menu_output .= "</ul></div>\n";

$menu_output = str_replace("'", '"', $menu_output);

/**
 * Print to screen
 */
echo $menu_output;
