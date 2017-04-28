<?php
/**
 * @package    TaskBundle\Event
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use TaskBundle\Entity\Note;
use TaskBundle\Event\Note\AddNoteEvent;
use TaskBundle\Event\Project\ConfirmAssigneeEvent;
use TaskBundle\Repository\ProjectRepository;

/**
 * Generates events based on entity name and action
 */
class EventFactory
{
    const ENTITY_PROJECT = 'project';
    const ENTITY_NOTE = 'note';
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @param $entityName
     * @param $action
     * @param array $extra
     * @return EventInChainInterface|GenericEvent
     * @throws Exception
     */
    public function createEvent($entityName, $action, array $extra = array())
    {
        $action = strtolower($action);

        switch ($entityName) {
            case self::ENTITY_PROJECT:
                // @todo we have only 2 actions now, so logic is simple
                if (!in_array($action, ['accept', 'cancel'])) {
                    throw new Exception(sprintf("Unknown action (%s) provided to entity (%s)", $action, $entityName));
                }
                if (empty($extra['id'])) {
                    throw new Exception(sprintf("Create event error: action (%s) needs project identifier", $action));
                }
                $project = $this->getProjectRepository()->find($extra['id']);
                if (null === $project) {
                    throw new Exception(sprintf("Create event error: project (%s) not found", $extra['id']));
                }
                $event = new ConfirmAssigneeEvent($project);
                $event->setArgument('action', $action);
                $event->setArgument('user', $extra['user']);
                return $event;
            case self::ENTITY_NOTE:
                if ($action != 'add') {
                    throw new Exception(sprintf("Unknown action (%s) for note event", $action));
                }
                $note = new Note();
                $note->setText($extra['message']);
                $note->setCreatedBy($extra['user']);

                $event = new AddNoteEvent($note);
                return $event;
            default:
                throw new Exception(sprintf("Unknown entity (%s) provided to event factory", $entityName));
        }
    }

    /**
     * @return ProjectRepository
     */
    public function getProjectRepository()
    {
        return $this->projectRepository;
    }
}
