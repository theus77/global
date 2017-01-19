<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;

class ContactType extends AbstractType
{
	
	private $domain;

	public function __construct($domain){
		$this->domain = $domain;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   	
    	$builder->add('name', TextType::class, [
					'translation_domain' => $this->domain,
    				'label_attr' => [
    						'class' => 'col-md-4 col-xs-12 col-sm-12'
    				],
					'label' => 'form.search.name',
			])
			->add('email', TextType::class, [
					'translation_domain' => $this->domain,
    				'label_attr' => [
    						'class' => 'col-md-4 col-xs-12 col-sm-12'
    				],
					'label' => 'form.search.email',
			])
			->add('honeypot', TextType::class, [
					'translation_domain' => $this->domain,
					'required' => false,
    				'label_attr' => [
    						'class' => 'col-md-4 col-xs-12 col-sm-12'
    				],
					'label' => 'form.honeypot',
			])
			->add('phone', TextType::class, [
					'required' => false,
					'translation_domain' => $this->domain,
    				'label_attr' => [
    						'class' => 'col-md-4 col-xs-12 col-sm-12'
    				],
					'label' => 'form.search.phone',
			])
			->add('remarks', TextareaType::class, [
					'required' => false,
					'translation_domain' => $this->domain,
    				'label_attr' => [
    						'class' => 'col-md-4 col-xs-12 col-sm-12'
    				],
					'attr' => [
							'rows' => 6,
					],
					'label' => 'form.search.remarks',
			])
    		->add('recaptcha', EWZRecaptchaType::class, [

    		])
			->add ( 'submit', SubmitType::class, [ 
					'attr' => [ 
							'class' => 'btn btn-primary' 
					],
					'translation_domain' => $this->domain,
					'label' => 'form.search.submit',
			] );
    }
}
