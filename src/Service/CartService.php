<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService 
{
    private const LESSONS_KEY = 'cart_lessons';
    private const CURSUSES_KEY = 'cart_cursuses';

    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addLesson(int $lessonId): void
    {
        $lessons = $this->session->get(self::LESSONS_KEY, []);
        if (!in_array($lessonId, $lessons)) {
            $lessons[] = $lessonId;
        }
        $this->session->set(self::LESSONS_KEY, $lessons);
    }

    public function removeLesson(int $lessonId): void
    {
        $lessons = $this->session->get(self::LESSONS_KEY, []);
        $lessons = array_diff($lessons, [$lessonId]);
        $this->session->set(self::LESSONS_KEY, $lessons);
    }

    public function addCursus(int $cursusId): void
    {
        $cursuses = $this->session->get(self::CURSUSES_KEY, []);
        if (!in_array($cursusId, $cursuses)) {
            $cursuses[] = $cursusId;
        }
        $this->session->set(self::CURSUSES_KEY, $cursuses);
    }

    public function removeCursus(int $cursusId): void
    {
        $cursuses = $this->session->get(self::CURSUSES_KEY, []);
        $cursuses = array_diff($cursuses, [$cursusId]);
        $this->session->set(self::CURSUSES_KEY, $cursuses);
    }

    public function getCart(): array
    {
        return [
            'lessons' => $this->session->get(self::LESSONS_KEY, []),
            'cursuses' => $this->session->get(self::CURSUSES_KEY, []),
        ];
    }

    public function getTotal(EntityManagerInterface $em): float
    {
        $total = 0;
        $lessons = $em->getRepository(\App\Entity\Lessons::class)->findBy([
            'id' => $this->session->get(self::LESSONS_KEY, [])
        ]);
        foreach ($lessons as $lesson) {
            $total += $lesson->getPrice();
        }

        $cursuses = $em->getRepository(\App\Entity\Cursus::class)->findBy([
            'id' => $this->session->get(self::CURSUSES_KEY, [])
        ]);
        foreach ($cursuses as $cursus) {
            $total += $cursus->getPrice();
        }
        return $total;
    }

    public function clearCart(): void
    {
        $this->session->remove(self::LESSONS_KEY);
        $this->session->remove(self::CURSUSES_KEY);
    }

}
