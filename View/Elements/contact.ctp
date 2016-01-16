<?php 

echo $this->Form->create('Booking', ['class' => 'form-horizontal']);

echo $this->element('form/textInput', [
		'field' => 'name',
		'label' => __('Nom et prénom *'),
		'placeholder' => __('nom et prénom'),
  		'required' => true
]);
echo $this->element('form/prependedCheckbox', [
		'field' => 'vat',
		'label' => __('Numéro de TVA'),
		'placeholder' => __('numéro de TVA'),
		'help' => __('Si d\'application')
]);
echo $this->element('form/textInput', [
		'field' => 'email',
		'label' => __('Adresse email *'),
		'placeholder' => __('email'),
  		'required' => true
]);
echo $this->element('form/textInput', [
		'field' => 'phone',
		'label' => __('Numéro de téléphone'),
		'placeholder' => __('téléphone')
]);
echo $this->element('form/textInput', [
		'field' => 'comment',
		'label' => __('Remarques'),
		'placeholder' => __('commentaires, remarques, questions, ...')
]);

echo $this->element('form/sendButton', [
]);

?>
</form>