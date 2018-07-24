<?php

namespace App\Serializer\Normalizer;

use App\Entity\Student;
use App\Entity\Teacher;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TeacherNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'course' => $object->getCourse()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Teacher;
    }
}
