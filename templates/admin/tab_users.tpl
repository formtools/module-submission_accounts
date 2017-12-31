  {ft_include file="messages.tpl"}

  <div class="margin_bottom_large">
    This tab lists all users that have logged in to view or update their submission.
  </div>

  {if $num_results == 0}

    <div class="notify yellow_bg" class="margin_bottom_large">
      <div style="padding:8px">
        No-one has logged in.
      </div>
    </div>

  {else}

    {$pagination}

    <table class="list_table" style="width:550px" cellpadding="1" cellspacing="1">
    <tr style="height: 20px;">
      <th width="50" class="nowrap pad_left pad_right">{$LANG.phrase_submission_id|upper}</th>
      <th>{$module_settings.username_field_label}</th>
      <th>{$LANG.phrase_last_logged_in}</th>
      <th width="60">{$LANG.word_view|upper}</th>
    </tr>
    {foreach from=$results item=submission name=row}
    <tr>
      <td align="center" class="medium_grey">{$submission.submission_id}</td>
      <td>{$submission.$username_col}</td>
      <td>
        {$submission.last_logged_in|custom_format_date:$account.timezone_offset:$account.date_format}
      </td>
      <td align="center"><a href="../../../admin/forms/edit_submission.php?submission_id={$submission.submission_id}&form_id={$form_id}&view_id={$submission_account.view_id}">{$LANG.word_view|upper}</a></td>
    </tr>
    {/foreach}
    </table>

    <p>
      <form action="{$same_page}" method="post">
        <input type="submit" name="clear_results" value="{$L.phrase_clear_results}" />
      </form>
    </p>

  {/if}
