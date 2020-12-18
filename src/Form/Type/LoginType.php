<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class LoginType
 * @package App\Form\Type
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
final class LoginType extends AbstractType
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Controls
        $builder->add('username', TextType::class, ['label' => 'Username']);
        $builder->add('password', PasswordType::class, ['trim' => true]);
        $builder->add('_remember_me',CheckboxType::class,
            [
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false
            ]
        );
        $builder->add('_target_path', HiddenType::class);
        $builder->add('button-submit', SubmitType::class, ['label' => 'Log In']);

        // Execute action
        $authUtils = $this->authenticationUtils;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($authUtils) {
                // Process errors if any
                $error = $authUtils->getLastAuthenticationError();
                if ($error) {
                    $event->getForm()->addError(new FormError($error->getMessage()));
                }

                // Update data with processed values
                $event->setData(
                    array_replace(
                        (array)$event->getData(),
                        array('username' => $authUtils->getLastUsername())
                    )
                );
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'csrf_protection' => true,
                'csrf_field_name' => '_csrf_token',
                'csrf_token_id' => 'authenticate'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return '';
    }

}
