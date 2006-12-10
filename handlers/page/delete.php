<?php
/**
 * Delete a page if the user is an admin.
 * 
 * @package     Handlers
 * @subpackage  Page
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Wakka::FormOpen()
 * @uses		Wakka::FormClose()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::Link()
 * @uses		Wakka::Query()
 * @uses		Wakka::Redirect()
 * @todo		move main <div> to templating class;
 */
echo '<div class="page">'."\n"; //TODO: move to templating class

/**
 * i18n
 */
if(!defined('ERR_NO_PAGE_DEL_RIGHT')) define ('ERR_NO_PAGE_DEL_RIGHT', 'You are not allowed to delete this page.');
if(!defined('PAGE_DELETION_SUCCESS')) define('PAGE_DELETION_SUCCESS', 'Page has been deleted!');
if(!defined('PAGE_DELETION_HEADER')) define('PAGE_DELETION_HEADER', 'Delete %s'); // %s - name of the page
if(!defined('PAGE_DELETION_TXT')) define('PAGE_DELETION_TXT', 'Completely delete this page, including all comments?');
if(!defined('BUTTON_CANCEL')) define ('BUTTON_CANCEL', 'Cancel');
if(!defined('BUTTON_DELETE')) define ('BUTTON_DELETE', 'Delete Page');

$tag = $this->GetPageTag();

if ($this->IsAdmin() || ($this->UserIsOwner($tag) && $this->config["owner_delete_page"] == 1))
{
    if ($_POST)
    {
        //  delete the page, comments, related links, acls and referrers 
        $this->Query("delete from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."comments where page_tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."referrers where page_tag = '".mysql_real_escape_string($tag)."'");

        // redirect back to main page
        $this->Redirect($this->config["base_url"], PAGE_DELETION_SUCCESS);
    }
    else
    {
        // show form
        ?>
        <h3><?php printf(PAGE_DELETION_HEADER,$this->Link($tag));?></h3>
        <br />

        <?php echo $this->FormOpen("delete") ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><?php echo PAGE_DELETION_TXT; ?></td>
            </tr>
            <tr>
                <td> <!-- nonsense input so form submission works with rewrite mode --><input type="hidden" value="" name="null"><input type="submit" value="<?php echo BUTTON_DELETE;?>"  style="width: 120px" />
                <input type="button" value="<?php echo BUTTON_CANCEL;?>" onclick="history.back();" style="width: 120px" /></td>
            </tr>
        </table>
        <?php
        print($this->FormClose());
    }
}
else
{
    echo '<em class="error">'.ERR_NO_PAGE_DEL_RIGHT.'</em>';
}
echo '</div>'."\n" //TODO: move to templating class
?>