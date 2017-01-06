<h2>Meta</h2>

<p><?php echo $this->Html->link('Find New Paths', array('action' => 'auto_add'));?> |
<?php echo $this->Html->link('Add', array('action' => 'add'));?></p>

<div class="vertical-align pull-right">
	<p class="pagination text-right">
		<?php echo $this->Paginator->counter(
		     '{{count}} results, showing {{start}} - {{end}}'
		);?> &nbsp;
	</p>
	
	<ul class="pagination pagination-sm">
		<?= $this->Paginator->first('«') ?>
		<?= $this->Paginator->prev('<') ?>
		<?= $this->Paginator->numbers(['modulus' => 4]) ?>
		<?= $this->Paginator->next('>') ?>
		<?= $this->Paginator->last('»') ?>
	</ul>
</div>

<table class="table table-hover">
<tr>
	<th><?php echo $this->Paginator->sort('path');?></th>
	<th><?php echo $this->Paginator->sort('title');?></th>
	<th><?php echo $this->Paginator->sort('description');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
	<th class="actions">actions</th>
</tr>
<?php
if(isset($data)):
	$a=0;
	foreach($data as $row):
		$actions = array();
		$actions[] = $this->Html->link('E', array('action' => 'edit', $row->id), array('title' => 'edit'));
		$actions[] = $this->Html->link('X', array('action' => 'delete', $row->id), array('title' => 'delete'), sprintf('Are you sure you want to delete the %s record?', $row->path));
		$actions = implode(' - ', $actions);
		$row->title = stripslashes($row->title);
		$row->description = stripslashes($row->description);
?>
<tr class="<?php echo $a%2==0?'even':'odd';?>">
<td><?php echo $row->path;?></td>

<td><?php echo $row->title;?></td>

<td><?php echo $row->description;?></td>

<td class="date"><?php echo $row->modified->i18nFormat('MMM d, Y h:mm a');?></td>

<td class="actions"><?php echo $actions;?></td>
</tr>
<?php
		$a++;
	endforeach;
endif;
?>
</table>
