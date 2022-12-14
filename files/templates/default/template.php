<?php
/*
Template name: Default
URI: http://www.projectsend.org/templates/default
Author: ProjectSend
Author URI: http://www.projectsend.org/
Author e-mail: contact@projectsend.org
Description: The default template uses the same style as the system backend, allowing for a seamless user experience
*/
$ld = "cftp_template"; // specify the language domain for this template

define("TEMPLATE_RESULTS_PER_PAGE", get_option("pagination_results_per_page"));

if (!empty($_GET["category"])) {
  $category_filter = $_GET["category"];
}

include_once ROOT_DIR . "/templates/common.php"; // include the required functions for every template

$window_title = __("File downloads", "cftp_template");

$page_id = "default_template";

$body_class = ["template", "default-template", "hide_title"];

include_once ADMIN_VIEWS_DIR . DS . "header.php";

define("TEMPLATE_THUMBNAILS_WIDTH", "50");
define("TEMPLATE_THUMBNAILS_HEIGHT", "50");
?>
<div class="row">
  <div class="col-xs-12">
    <div id="wrapper">
      <div id="right_column">

        <div class="form_actions_left">
          <div class="form_actions_limit_results">
            <?php show_search_form(); ?>

            <?php if (!empty($cat_ids)) { ?>
              <form action="" name="files_filters" method="get" class="form-inline form_filters">
                <?php form_add_existing_parameters([
                  "category",
                  "action",
                ]); ?>
                <div class="form-group group_float">
                  <select name="category" id="category" class="txtfield form-control">
                    <option value="0"><?php _e(
                                        "Choose Plugin",
                                        "cftp_admin"
                                      ); ?></option>
                    <?php
                    $selected_parent = isset(
                      $category_filter
                    )
                      ? [$category_filter]
                      : [];
                    echo generate_categories_options(
                      $get_categories["arranged"],
                      0,
                      $selected_parent,
                      "include",
                      $cat_ids
                    );
                    ?>
                  </select>
                </div>
                <button type="submit" id="btn_proceed_filter_files" class="btn btn-sm btn-default"><?php _e(
                                                                                                      "Filter",
                                                                                                      "cftp_admin"
                                                                                                    ); ?></button>
              </form>
            <?php } ?>
          </div>
        </div>

        <form action="" name="files_list" method="get" class="form-inline batch_actions">
          <?php form_add_existing_parameters(); ?>
          <div class="form_actions_right">
            <div class="form_actions">
              <div class="form_actions_submit">
                <div class="form-group group_float">
                  <label class="control-label hidden-xs hidden-sm"><i class="glyphicon glyphicon-check"></i> <?php _e(
                                                                                                                "Selected files actions",
                                                                                                                "cftp_admin"
                                                                                                              ); ?>:</label>
                  <select name="action" id="action" class="txtfield form-control">
                    <?php
                    $actions_options = [
                      "none" => __(
                        "Select action",
                        "cftp_admin"
                      ),
                      "zip" => __(
                        "Download zipped",
                        "cftp_admin"
                      ),
                    ];
                    foreach ($actions_options
                      as $val => $text) { ?>
                      <option value="<?php echo $val; ?>"><?php echo $text; ?></option>
                    <?php }
                    ?>
                  </select>
                </div>
                <button type="submit" id="do_action" class="btn btn-sm btn-default"><?php _e(
                                                                                      "Proceed",
                                                                                      "cftp_admin"
                                                                                    ); ?></button>
              </div>
            </div>
          </div>

          <div class="right_clear"></div><br />

          <div class="form_actions_count">
            <?php $count = isset($count_for_pagination)
              ? $count_for_pagination
              : 0; ?>
            <p><?php echo sprintf(
                  __("Found %d elements", "cftp_admin"),
                  (int) $count
                ); ?></p>
          </div>

          <div class="right_clear"></div>

          <?php
          if (!isset($count_for_pagination)) {
            if (isset($no_results_error)) {
              switch ($no_results_error) {
                case "search":
                  $no_results_message = __(
                    "Your search keywords returned no results.",
                    "cftp_admin"
                  );
                  break;
              }
            } else {
              $no_results_message = __(
                "There are no files available.",
                "cftp_template"
              );
            }
            echo system_message("danger", $no_results_message);
          }

          if (isset($count) && $count > 0) {
            /**
             * Generate the table using the class.
             */
            $table_attributes = [
              "id" => "files_list",
              "class" => "footable table",
            ];
            $table = new \ProjectSend\Classes\TableGenerate(
              $table_attributes
            );

            $conditions = [
              "total_downloads" =>
              CURRENT_USER_LEVEL != "99" && !isset($search_on)
                ? true
                : false,
              "is_search_on" => isset($search_on) ? true : false,
            ];

            $thead_columns = [
              [
                "select_all" => true,
                "attributes" => [
                  "class" => ["td_checkbox"],              
                  "style" => ["text-align: center;"]
                ],
                "condition" => $conditions["select_all"],
              ],
              [
                "sortable" => true,
                "sort_url" => "timestamp",
                "sort_default" => true,
                "content" => __("Added On", "cftp_admin"),
              ],
              [
                "sortable" => true,
                "sort_url" => "categories",
                "content" => __("Plugin", "cftp_admin"),
              ],
              [
                "sortable" => true,
                "sort_url" => "filename",
                "content" => __("Title", "cftp_admin"),
              ],
              [
                "sort_url" => "description",
                "content" => __("Description", "cftp_admin"),
                "hide" => "phone",
                "attributes" => [
                  "class" => ["description"],
                ],
              ],
              [
                "sortable" => true,
                "sort_url" => "uploader",
                "content" => __("Uploader", "cftp_admin"),
                "condition" => $conditions["is_not_client"],
              ],
              // [
              //   "content" => __("Size", "cftp_admin"),
              //   "hide" => "phone",
              // ],
              // [
              //   "sortable" => true,
              //   "sort_url" => "download_count",
              //   "content" => __("Downloads", "cftp_admin"),
              //   "hide" => "phone",
              //   "condition" => $conditions["total_downloads"],
              // ],
              [
                "content" => __("Download", "cftp_admin"),
                "hide" => "phone",
              ],
            ];

            $table->thead($thead_columns);

            foreach ($available_files as $file_id) {
              $file = new \ProjectSend\Classes\Files();
              $file->get($file_id);

              $table->addRow();

              /**
               * Prepare the information to be used later on the cells array
               */

              /** Checkbox */
              $checkbox =
                $file->expired == false
                ? '<input type="checkbox" name="files[]" value="' .
                $file->id .
                '" class="batch_checkbox" />'
                : null;

              /** File title */
              $file_title_content =
                "<strong>" . $file->title . "</strong>";
              if ($file->expired == false) {
                $filetitle =
                  '<a href="' .
                  $file->download_link .
                  '">' .
                  $file_title_content .
                  "</a>";
              } else {
                $filetitle = $file_title_content;
              }

              /** Extension */
              $extension_cell =
                '<span class="label label-success label_big">' .
                $file->extension .
                "</span>";

              /** Date */
              $date = format_date($file->uploaded_date);

              /** Expiration */
              if ($file->expires == "1") {
                if ($file->expired == false) {
                  $class = "primary";
                } else {
                  $class = "danger";
                }

                $value = date(
                  get_option("timeformat"),
                  strtotime($file->expiry_date)
                );
              } else {
                $class = "success";
                $value = __("Never", "cftp_template");
              }

              $expiration_cell =
                '<span class="label label-' .
                $class .
                ' label_big">' .
                $value .
                "</span>";

              /** Thumbnail */
              $preview_cell = "";
              if ($file->expired == false) {
                if ($file->isImage()) {
                  $thumbnail = make_thumbnail(
                    $file->full_path,
                    null,
                    TEMPLATE_THUMBNAILS_WIDTH,
                    TEMPLATE_THUMBNAILS_HEIGHT
                  );
                  if (!empty($thumbnail["thumbnail"]["url"])) {
                    $preview_cell =
                      '
                                                <a href="#" class="get-preview" data-url="' .
                      BASE_URI .
                      "process.php?do=get_preview&file_id=" .
                      $file->id .
                      '">
                                                    <img src="' .
                      $thumbnail["thumbnail"]["url"] .
                      '" class="thumbnail" alt="' .
                      $file->title .
                      '" />
                                                </a>';
                  }
                } else {
                  if ($file->embeddable) {
                    $preview_cell =
                      '<button class="btn btn-warning btn-sm btn-wide get-preview" data-url="' .
                      BASE_URI .
                      "process.php?do=get_preview&file_id=" .
                      $file->id .
                      '">' .
                      __("Preview", "cftp_admin") .
                      "</button>";
                  }
                }
              }

              /** Download */
              if ($file->expired == true) {
                $download_link = "javascript:void(0);";
                $download_btn_class =
                  "btn btn-danger btn-sm disabled";
                $download_text = __("File expired", "cftp_template");
              } else {
                $download_btn_class =
                  "btn btn-primary btn-sm btn-wide";
                $download_text = __("Download", "cftp_template");
              }
              $download_cell =
                '<a href="' .
                $file->download_link .
                '" class="' .
                $download_btn_class .
                '">' .
                $download_text .
                "</a>";

              

          /**
           * Download count when filtering by group or client
           */
          if (isset($search_on)) {
            $download_count_content =
              $row["download_count"] .
              " " .
              __("times", "cftp_admin");

            switch ($results_type) {
              case "client":
                break;
              case "group":
              case "category":
                $download_count_class =
                  $row["download_count"] > 0
                  ? "downloaders btn-primary"
                  : "btn-default disabled";
                $download_count_content =
                  '<a href="' .
                  BASE_URI .
                  "download-information.php?id=" .
                  $file->id .
                  '" class="' .
                  $download_count_class .
                  ' btn btn-sm" title="' .
                  html_output($row["filename"]) .
                  '">' .
                  $download_count_content .
                  "</a>";
                break;
            }
          }

          /**
           * Download count and link on the unfiltered files table
           * (no specific client or group selected)
           */
          if (!isset($search_on)) {
            if (CURRENT_USER_LEVEL != "99") {
              if ($row["download_count"] > 0) {
                $btn_class = "downloaders btn-primary";
              } else {
                $btn_class = "btn-default disabled";
              }

              $downloads_table_link =
                '<a href="' .
                BASE_URI .
                "download-information.php?id=" .
                $file->id .
                '" class="' .
                $btn_class .
                ' btn btn-sm" title="' .
                html_output($row["filename"]) .
                '">' .
                $row["download_count"] .
                " " .
                __("downloads", "cftp_admin") .
                "</a>";
            }
          }
              $file->categories_name = implode(', ', $file->categories_name);
              $tbody_cells = [
                [
                  "content" => $checkbox,
                  "attributes" => [
                    "style" => ["width: 2%;"],
                    "style" => ["width: 2%; text-align: center;"]
                  ],
                ],
                [
                  "content" => $date,
                  "attributes" => [
                    "style" => ["width: 5%;"]
                  ],
                ],
                [
                  // "content" => $categories /**FIX THIS */,
                  "attributes" => [
                    "class" => ["category_id"],
                    "style" => ["width: 10%;"],
                  ],
                  "content" => $file->categories_name,
                ],
                [
                  "content" => $filetitle,
                  "attributes" => [
                    "class" => ["file_name"],
                    "style" => ["width: 18%;"]
                  ],
                ],
                [
                  "content" => $file->description,
                  "attributes" => [
                    "class" => ["description"],
                    "style" => ["width: 40%;"]
                  ],
                ],
                [
                  "content" => $file->uploaded_by,
                  "condition" => $conditions["is_not_client"],
                  "attributes" => [
                    "style" => ["width: 7%;"]
                  ],
                ],
                // [
                //   "content" => $file->size_formatted,
                //   "attributes" => [
                //     "style" => ["width: 4%;"]
                //   ],
                // ],
                // [
                //   "content" => !empty($downloads_table_link)
                //     ? $downloads_table_link
                //     : false,
                //   "condition" => $conditions["total_downloads"],
                //   "attributes" => [
                //     "class" => ["text-center"],
                //     "style" => ["width: 7%; text-align: center;"]
                //   ],
                // ],
                [
                  "content" => $download_cell,
                  "attributes" => [
                    "class" => ["text-center"],
                    "style" => ["width: 7%; text-align: center;"]
                  ],
                ],
              ];

              foreach ($tbody_cells as $cell) {
                $table->addCell($cell);
              }

              $table->end_row();
            }

            echo $table->render();

            /**
             * PAGINATION
             */
            echo $table->pagination([
              "link" => "my_files/index.php",
              "current" => $pagination_page,
              "item_count" => $count_for_pagination,
              "items_per_page" => TEMPLATE_RESULTS_PER_PAGE,
            ]);
          }
          ?>
        </form>

      </div> <!-- right_column -->
    </div> <!-- wrapper -->
  </div> <!-- row -->
</div> <!-- container -->

<?php default_footer_info(); ?>
<?php
render_json_variables();
load_js_files();
?>
</body>

</html>