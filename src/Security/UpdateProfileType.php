<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class UpdateProfileType
 * @package App\Security
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
class UpdateProfileType extends AbstractType
{

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', TextType::class);
        $builder->add('password', RepeatedType::class, [
            'first_name' => 'password',
            'second_name' => 'confirm',
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'New password'
            ],
            'second_options' => [
                'label' => 'Confirm password'
            ],
            'required' => false,
            'constraints' => new Length(['min' => 6])
        ]);
        $builder->add('button-submit', SubmitType::class, ['label' => 'Submit']);
    }

}
