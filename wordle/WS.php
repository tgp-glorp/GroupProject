<?php
require __DIR__ . '..\..\vendor\autoload.php';  // Include the Composer autoloader

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// Database connection class
class DatabaseConnection
{
    private $conn;

    public function __construct()
    {
        $serverName = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbName = "mydb";
        $this->conn = new mysqli($serverName, $db_username, $db_password, $dbName);
        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed");
        }
    }

    public function getConn()
    {
        return $this->conn;
    }

    public function closeConn()
    {
        $this->conn->close();
    }
}


class Chat implements MessageComponentInterface
{
    private $page;

    private $conn = null;  // Database connection stored in the class

    private $clients = [

    ];

    private $db;

    private $user_data = [];

    private $opp;

    public function __construct()
    {
        $this->db = new DatabaseConnection();  // Initialize the $db property
        $this->conn = $this->db->getConn();  // Use the connection from $db
    }
    public function onOpen(ConnectionInterface $conn)
    {
        //$conn->send(json_encode(["action" => "ping", "message" => "Ping!"]));
        echo "New connection resourceId:" . $conn->resourceId;
        $sessionQuery = $conn->httpRequest->getUri()->getQuery();
        parse_str($sessionQuery, $params);
        if ($params["type"] == "Wordle") {
            $this->clients[$conn->resourceId] = $conn;
            $session_id = $params['session_id'];
            $this->page = "wordle";
            // Retrieving user based on session ID 
            $user_id = $this->getUserFromSession($session_id);

            if ($user_id) {
                // Store user information in memory
                $this->user_data[$conn->resourceId] = ['user_id' => $user_id];

                echo "User ID: $user_id \n";
            } else {
                echo "Session not found or invalid.\n";
            }

        }
    }
    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Message from {$from->resourceId}: $msg\n";

        // Respond to a pong message
        // if ($data["action"] == "pong") {
        //     sleep(1);
        //     $from->send(json_encode(["action" => "ping", "message" => "Ping!"]));
        // }
        try {
            $data = json_decode($msg, true);
            echo "Message from {$from->resourceId}: $msg\n";
            if ($data === null) {
                $from->send("Invalid JSON received.");
                return;
            }
            if ($data["action"] == "getUserData" && $this->page == "wordle") {
                $userId = $this->user_data[$from->resourceId]["user_id"];
                echo "resourceId" . $from->resourceId;
                echo $this->user_data[$from->resourceId]["user_id"];
                $userData = $this->getUserInfo($userId);
                echo $userData["username"];
                $userData = $userData + [
                    "action" => "userData"
                ];
                $userData = json_encode($userData);
                $from->send($userData);
                $this->page = null;
            }
            if ($data["action"] == "checkName") {
                $name = $this->checkName($data["data"]);
                $response = [
                    "action" => "checkName",
                    "response" => $name
                ];
                $response = json_encode($response);
                $from->send($response);
            }
            if ($data["action"] == "checkMail") {
                $mail = $this->checkMail($data["data"]);
                $response = [
                    "action" => "checkMail",
                    "response" => $mail
                ];
                $response = json_encode($response);
                $from->send($response);
            }
            if ($data["action"] == "logMail") {
                $mail = $this->checkMail($data["data"]);
                $response = [
                    "action" => "logMail",
                    "response" => $mail
                ];
                $response = json_encode($response);
                $from->send($response);
            }
            $word = "";
            if ($data["action"] == "GetWord") {
                $word = $this->getWord();
                $response = [
                    "action" => "word",
                    "response" => $word
                ];
                $response = json_encode($response);
                $from->send($response);
            }
            if ($data["action"] == "1PChal") {
                $word = $this->getChallenge();
                $response = [
                    "response" => $word,
                    "action" => "word"
                ];
                $response = json_encode($response);
                $from->send($response);
            }
            if ($data["action"] == "CheckWord") {
                $input = htmlspecialchars($data["data"]);
                $input = trim($input);
                $check = $this->checkWord($input);
                $response = [
                    "response" => $check,
                    "action" => "check"
                ];
                $response = json_encode($response);
                $from->send($response);
            }
            if ($data["action"] == "TimedChallenge") {
                $username = htmlspecialchars($data["data"]);
                $targetID = $this->getID($username);
                echo $targetID;
                if (!isset($this->user_data[$from->resourceId])) {
                    echo "Error: data not found for resource ID {$from->resourceId}\n";
                    echo "Current user_data: " . print_r($this->user_data[$from->resourceId], true) . "\n";
                    return;
                }
                $senderId = $this->user_data[$from->resourceId]["user_id"];
                echo "Sender ID:" . print_r($senderId, true);
                $sender = $this->getName($senderId);
                $challengeData = [
                    "action" => "ReceiveChallenge",
                    "message" => "You have been challenged by $sender",
                    "from" => $senderId
                ];
                $client = $this->getUser($targetID);
                echo "[" . date("H:i:s") . "] 2nd try receiver " . $client->resourceId;
                $challengeData = json_encode($challengeData);
                $client->send($challengeData);
                echo "[" . date("H:i:s") . "] 2nd try receiver " . $client->resourceId;
                $this->opp = $client;
                echo "Sender" . $this->user_data[$from->resourceId]["user_id"];
                $client->opp = $from;
                echo "Receiver" . $this->user_data[$client->resourceId]["user_id"];
            }
            if ($data["action"] == "resetWordle") {
                $targetId = $data["data"];
                $client = $this->getUser($targetId);
                $word = $this->getWord();
                $response = [
                    "action" => "resetWordle",
                    "response" => $word
                ];
                $response = json_encode($response);
                $from->send($response);
                $client->send($response);
            }
            if ($data["action"] == "gameDone") {
                $userId = $this->user_data[$from->resourceId]["user_id"];
                $this->gamePlayed($userId);
            }
            if ($data["action"] == "winner") {
                $userId = $this->user_data[$from->resourceId]["user_id"];
                $this->gamePlayed($userId);
                $this->gameWon($userId);
                if ($data["data"]) {
                    $opponent = $this->getUser($data["data"]);
                    $oppId = $this->user_data[$opponent->resourceId]["user_id"];
                    $this->gamePlayed($oppId);
                }
            }
            if ($data["action"] == "endGame") {
                $opp=$from;
                if($this->opp){
                    $opp = $this->opp;
                    $oppId = $this->user_data[$opp->resourceId]["user_id"];
                    $oppRes = $opp->resourceId;
                    echo "\n" . $oppId . " in endGame \n";
                    echo $oppRes . "in endGame connection id \n";
                }
                if ($data["data"]) {
                    $opp = $this->getUser($data["data"]);
                    echo $opp->resourceId . "trying sender";
                }
                if (!$opp) {
                    echo "error";
                    return;
                }
                $endGame = [
                    "action" => "endGame",
                ];
                echo "[" . date("H:i:s") . "] Sending endGame message to " . $opp->resourceId;
                $endGame = json_encode($endGame);
                $opp->send($endGame);
                echo "[" . date("H:i:s") . "] Sent endGame message to " . $opp->resourceId;
            }
        } catch (\Exception $e) {
            echo "Error in onMessage: " . $e->getMessage() . "\n";
        }

    }
    public function onClose(ConnectionInterface $conn)
    {

        echo "Connection {$conn->resourceId} has disconnected\n";

        unset($this->clients[$conn->resourceId]);
        if (isset($this->user_data[$conn->resourceId])) {
            unset($this->user_data[$conn->resourceId]);
        }

        if ($this->opp === $conn) {
            $this->opp = null;  // Clear stale opponent reference
        }


    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
    }
    private function getUser($target)
    {
        foreach ($this->clients as $client) {
            if ($client instanceof ConnectionInterface) {
                $user_id = $this->user_data[$client->resourceId]['user_id'];
                echo "User ID: " . $user_id . " Target ID: " . $target . "\n";
                if ($user_id == $target) {
                    return $client;
                }
            }
        }
        echo "USER NOT ACTIVE OR NOT FOUND";
        return null;
    }
    private function getUserInfo($userId)
    {
        $sql = "SELECT id,username,games_played,games_won
                FROM users 
                WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        }

        return null;
    }
    private function getUserFromSession($session_id)
    {
        $sql = "SELECT user_id 
                FROM user_sessions 
                WHERE session_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['user_id'];  // Return the user_id associated with the session_id
        }

        return null;
    }

    private function checkName(string $username)
    {
        $conn = $this->conn;
        if ($conn) {
            $sql = "SELECT *
                    From users
                    where username=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return true;
            }
            return false;

        } else {
            return "DBERROR";
        }
    }
    private function checkMail(string $email)
    {
        $conn = $this->conn;
        if ($conn) {
            $sql = "SELECT *
                    From users
                    where email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return true;
            }
            return false;

        } else {
            return "DBERROR";
        }
    }

    private function getID(string $username)
    {
        if ($this->conn) {
            $sql = "SELECT id
                    From users
                    where username=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['id'];
            }

            $stmt->close();
        } else {
            return "DBERROR";
        }
    }
    private function getName(int $id)
    {
        $conn = $this->conn;
        if ($conn) {
            $sql = "SELECT username
            From users
            where id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['username'];
            }

            $stmt->close();
        } else {
            return "DBERROR";
        }

    }

    private function getWord()
    {
        if ($this->conn) {
            $sql = "SELECT word FROM words ORDER BY RAND() LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $wordToShow = $row["word"];
                return $wordToShow;
            }

            $stmt->close();
        } else {
            return "DBERROR";
        }
    }

    private function getChallenge()
    {
        $conn = $this->conn;
        if ($conn) {
            $user_id = $_SESSION["user_id"];
            echo $user_id;
            $sql = "SELECT word_to_guess FROM challenges where user_id = ? AND is_completed=false ORDER BY RAND() LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Challenge 
                $row = $result->fetch_assoc();
                $challenge_word = $row['word_to_guess'];
                return $challenge_word;
            } else {
                return "You have no active challenges.";
            }
        }
    }
    private function checkWord(string $input)
    {
        $conn = $this->conn;
        if ($conn) {
            $sql = "SELECT word
                    FROM words
                    where word=?"
            ;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $input); // Bind the parameter
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Word exists in the database
                return 'success';
            } else {
                // Word doesn't exist in the database
                return 'not_found';
            }
        } else {
            return "connError";
        }
    }
    private function gameWon($userId)
    {
        $conn = $this->conn;
        if ($conn) {
            $sql = "UPDATE users
                  SET games_won=games_won+1 
                  where id=?  ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId); // Bind the parameter
            if ($stmt->execute()) {
                echo "winner is" . $userId;
            } else {
                echo "EXECUTIONERROR";
            }
        }
    }
    private function gamePlayed($userId)
    {
        $conn = $this->conn;
        if ($conn) {
            $sql = "UPDATE users
                  SET games_played=games_played+1 
                  where id=?  ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId); // Bind the parameter
            if ($stmt->execute()) {
                echo "Player is" . $userId;
            } else {
                echo "EXECUTIONERRORGPLAYED";
            }
        }
    }
}

// Define the WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()  // Chat should implement Ratchet\MessageComponentInterface
        )
    ),
    8080  // Port number
);

// Run the server
$server->run();
