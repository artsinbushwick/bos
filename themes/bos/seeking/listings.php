<?php

global $bos;
$seeking = $bos->seeking;

?>
<div id="spaces-seeking-artists">
  <h3>Spaces (or Shows) Seeking Artists</h3>
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th class="title">Space/Show</th>
      <th class="description">Description</th>
      <th class="seeking">Seeking...</th>
    </tr>
    <?php $seeking->get_seeking('space'); ?>
  </table>
</div>
<div id="artists-seeking-space">
  <h3>Artists Seeking Space</h3>
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th class="title">Artist</th>
      <th class="description">Description</th>
      <th class="seeking">Seeking...</th>
    </tr>
    <?php $seeking->get_seeking('artist'); ?>
  </table>
</div>
