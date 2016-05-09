<?php 

echo $this->Form->create('Booking', ['class' => 'form-horizontal']);

echo $this->element('form/textInput', [
		'field' => 'name',
		'label' => __('Nom et prénom / Organisme ou société *'),
  		'required' => true
]);
/*
echo $this->element('form/prependedCheckbox', [
		'field' => 'vat',
		'label' => __('Numéro de TVA'),
		'placeholder' => __('numéro de TVA'),
		'help' => __('Si d\'application')
]);
*/
echo $this->element('form/textInput', [
		'field' => 'email',
		'label' => __('Adresse email *'),
  		'required' => true
]);
echo $this->element('form/textInput', [
		'field' => 'phone',
		'label' => __('Numéro de téléphone')
]);
echo $this->element('form/textInput', [
		'field' => 'comment',
		'label' => __('Remarques')
]);

echo $this->element('form/sendButton', [
]);

?>
</form>