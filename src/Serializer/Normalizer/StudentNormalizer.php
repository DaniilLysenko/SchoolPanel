<?php

namespace App\Serializer\Normalizer;
use App\Entity\Student;
use App\Entity\Teacher;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class StudentNormalizer implements NormalizerInterface
{
    private $teacherNormalizer;
    public function __construct(TeacherNormalizer $teacherNormalizer)
    {
        $this->teacherNormalizer = $teacherNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'age' => $object->getAge(),
            'sex' => $object->getSex(),
            'phone' => $object->getPhone(),
            'avatar' => $object->getAvatar(),
            'teachers' => array_map(
                function ($object) use ($format, $context) {
                    return $this->teacherNormalizer->normalize($object, $format, $context);
                },
                $object->getStudentTeacher()->getValues()
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Student;
    }
}