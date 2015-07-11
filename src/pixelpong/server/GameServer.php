<?php

namespace stigsb\pixelpong\server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;

class GameServer implements MessageComponentInterface
{
    /** @var LoopInterface */
    private $loop;

    /** @var GameLoop */
    private $gameLoop;

    /** @var \SplObjectStorage */
    private $connections;

    /** @var FrameBuffer */
    private $frameBuffer;

    public function __construct(LoopInterface $loop, FrameBuffer $frameBuffer)
    {
        $this->loop = $loop;
        $this->frameBuffer = $frameBuffer;
        $this->update_timer = $this->loop->addPeriodicTimer(0.2, [$this, 'onFrameUpdate']);
        $this->gameLoop = new PressStartToPlayGameLoop($frameBuffer);
        $this->connections = new \SplObjectStorage();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $frameEncoder = new JsonFrameEncoder($this->frameBuffer);
//        $frameEncoder = new AsciiFrameEncoder($this->frameBuffer);
        $playerConnection = new ActivePlayerPlayerConnection($frameEncoder);
        $this->connections->attach($conn, $playerConnection);
        foreach ($this->connections as $conn) {
            /** @var PlayerConnection $playerConnection */
            $playerConnection = $this->connections[$conn];
            print "conn: "; var_dump(get_class($conn));
            print "playerConnection: "; var_dump(get_class($playerConnection));
            $conn->send($playerConnection->getFrameEncoder()->encodeFrameInfo($this->frameBuffer));
        }
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        printf("Disconnected\n");
        $this->connections->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        printf("ERROR\n");
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        printf("incoming message: $msg\n");
    }

    function onFrameUpdate(TimerInterface $timer)
    {
        $utime = microtime(true);
        $time = (int)$utime;
        $ms = (int)(($utime - $time) * 1000);
        printf("== frameupdate: %s.%03d\n", strftime('%H:%M:%S', $time), $ms);
        $this->gameLoop->onFrameUpdate();
        $frame = $this->frameBuffer->getAndSwitchFrame();
        $encodedFrameCache = [];
        foreach ($this->connections as $conn) {
            /** @var PlayerConnection $playerConnection */
            $playerConnection = $this->connections[$conn];
            $encoder = $playerConnection->getFrameEncoder();
            $key = get_class($encoder);
            if (!isset($encodedFrameCache[$key])) {
                $encodedFrameCache[$key] = $playerConnection->getFrameEncoder()->encodeFrame($frame);
            }
//            printf("message: %s\n", $encodedFrameCache[$key]);
            $conn->send($encodedFrameCache[$key]);
        }
    }

    public function onEvent(Event $event)
    {
        $this->gameLoop->onEvent($event);
    }

    public function switchToGameLoop(GameLoop $gameLoop)
    {
        $this->gameLoop = $gameLoop;
        $gameLoop->onEnter();
    }

}
