<?php
/*
Plugin Name: AutoTags
Description: Wordpress plugin for tag suggestions using Yahoo CAS api 
Author: Jaric 
Version: 1.0
License: GPL
Author URI: http://blog.jaric.tw
*/
?>
<?php
/*
Copyright (C) 2009 blog.jaric.tw

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function autoTags_scripts () {
 
   $uri = $_SERVER['REQUEST_URI'];
   $pathinfo = pathinfo($uri);
   $ajax_path = get_option('siteurl') . '/wp-content/plugins/autotags/AutoTagsAjax.php';

   load_plugin_textdomain('AutoTags', 'wp-content/plugins/autotags');

   if (strpos($pathinfo["basename"], 'post.php') === 0 ||
       strpos($pathinfo["basename"], 'post-new.php') === 0) {
      echo 
      '<script type="text/javascript">
         /*<![CDATA[*/

         var autoTags_fetchTags    = "' . __("Fetch tags", "AutoTags") . '";
         var autoTags_fetchingTags = "' . __("Fetching tags...", "AutoTags") . '";
         var autoTags_poweredBy    = "' . __("powered by", "AutoTags") . '";
         
         addLoadEvent(function() {
            
            jQuery("#tagsdiv .inside").prepend("<input type=\"button\" class=\"button\" value=\"" + autoTags_fetchTags + "\" id=\"autotags\" style=\"vertical-align: middle;\" /> " +
            autoTags_poweredBy + " <a href=\"http://blog.jaric.tw/\">Jaric</a>");
            jQuery("#autotags").click(function(e) {

               var content = (typeof tinyMCE == "undefined" || 
                              typeof tinyMCE.getInstanceById("content") == "undefined" ||
                              tinyMCE.getInstanceById("content").isHidden()) ? 
                              "<div>" + jQuery("#content").val() + "</div>" : 
                              tinyMCE.getInstanceById("content").getContent();


               var text = jQuery("#title").val() + " " + 
                          ((content.search(/\\S/) != -1) ? jQuery(content).text() : "") + " " +
                          jQuery("#excerpt").val();

               if (text.search(/\\S/) != -1) {

                  jQuery(this).val(autoTags_fetchingTags);
                  
                  jQuery.post("' . $ajax_path .'", { text: text }, 

                     function(data) {
                        jQuery("#newtag").focus();

                        var fetchedTags = data.memes.dimensions.topic || [];
                        if (data.memes.dimensions.topic)
                           fetchedTags = fetchedTags.concat(data.memes.dimensions.topic);
                        
                        var oldTags     = jQuery.grep(jQuery("#tags-input").val().split(/\s*,\s*/), function(obj, index) { return (obj != ""); });
                        var currentTags = jQuery.grep(jQuery("#newtag").val().split(/\s*,\s*/), function(obj, index) { return (obj != ""); });
                       
                        for (var i = 0; i < fetchedTags.length; i++) {
                           if (fetchedTags[i] != null &&
                               fetchedTags[i].search(/\\S/) != -1 &&
                               jQuery.inArray(fetchedTags[i], oldTags) == -1 &&
                               jQuery.inArray(fetchedTags[i], currentTags) == -1) {
                              currentTags.push(fetchedTags[i]);
                           }
                        }
                       
                        jQuery("#newtag").val(currentTags.join(", "));
                        jQuery("#autotags").val(autoTags_fetchTags); 
                     },
                     
                     "json"
                  );
               }
            });
         });

         /*]]>*/
      </script>';
   }
   
}
add_action('admin_print_scripts', 'autoTags_scripts');
?>
