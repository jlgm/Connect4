<?php
//done
class Board extends CI_Controller {
     
    function __construct() {
        // Call the Controller constructor
        parent::__construct();
        session_start();
    }

    public function _remap($method, $params = array()) {
        // enforce access control to protected functions

        if (!isset($_SESSION['user']))
            redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
         
        return call_user_func_array(array($this, $method), $params);
    }
    
    function index() {
        $data["main"] = "match/board";
        $data["script"] = "match/js";
        $data["data"] = $data;
        
        $user = $_SESSION['user'];
         
        $this->load->model('user_model');
        $this->load->model('invite_model');
        $this->load->model('match_model');
         
        $user = $this->user_model->get($user->login);

        $invite = $this->invite_model->get($user->invite_id);
         
        if ($user->user_status_id == User::WAITING) {
            $invite = $this->invite_model->get($user->invite_id);
            $otherUser = $this->user_model->getFromId($invite->user2_id);
            
            $data["match_status"] = Match::ACTIVE;
            $data["board"] = new Board_model();
            $data["board"]->initialize(7);
            $data["num"] = Board_model::P1;
            $data["color"] = "red";
        }
        else if ($user->user_status_id == User::PLAYING) {
            $match = $this->match_model->get($user->match_id);
            if ($user->id == $match->user1_id) {
                $otherUser = $this->user_model->getFromId($match->user2_id);
                $data["num"] = Board_model::P1;
                $data["color"] = "red";
            } else {
                $otherUser = $this->user_model->getFromId($match->user1_id);
                $data["num"] = Board_model::P2;
                $data["color"] = "yellow";
            }
            $data["match_status"] = $match->match_status_id;
            $data["board"] = unserialize(base64_decode($match->board_state));
        }
         
        $data['user']=$user;
        $data['otherUser']=$otherUser;
         
        if ($user->user_status_id == User::PLAYING) $data['status'] = 'playing';
        if ($user->user_status_id == User::WAITING) $data['status'] = 'waiting';
        
        $data["data"] = $data;

        $this->load->view("template", $data);
    }
    
    function move($column) {
        $this->load->model('user_model');
        $this->load->model('match_model');
        
        $user = $_SESSION['user'];
        
        $user = $this->user_model->get($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg="Invalid move!";
            goto error;
        }
        
        $this->db->trans_begin();
        
        $match = $this->match_model->getExclusive($user->match_id);
        
        if ($match->user1_id == $user->id)  $match->move(Board_model::P1, $column) ;
        else $match->move(Board_model::P2, $column); 
        
        if ($match->won($column)) {
            $win_state = ($match->user1_id == $user->id ? Match::U1WON : Match::U2WON);
            $this->match_model->updateStatus($match->id, $win_state);
        }
        
        $this->match_model->updateState($match->id, $match->board_state);
        $this->match_model->updateMsgU1($match->id, $msg);
        $this->match_model->updateMsgU2($match->id, $msg);
        
        if ($this->db->trans_status() === FALSE) {
            $errormsg = "Transaction error";
            goto transactionerror;
        }
         
        $this->db->trans_commit();
         
        echo json_encode(array('status'=>'success','message'=>$msg));
        goto end;
        
        transactionerror:
        $this->db->trans_rollback();
        
        error:
        echo json_encode(array('status'=>'failure','message'=>$errormsg));
        end:
    }
    
    
    function getState() {
        $user = $_SESSION['user'];
         
        $this->load->model('user_model');
        $this->load->model('invite_model');
        $this->load->model('match_model');
        
        $user = $this->user_model->get($user->login);
        
        if ($user->user_status_id == 5) {
            $match = $this->match_model->get($user->match_id);
            if ($user->id == $match->user1_id) {
                $otherUser = $this->user_model->getFromId($match->user2_id);
                $data["num"] = Board_model::P1;
                $data["color"] = "red";
            } else {
                $otherUser = $this->user_model->getFromId($match->user1_id);
                $data["num"] = Board_model::P2;
                $data["color"] = "yellow";
            }
            $data["match_status"] = $match->match_status_id;
            $data["board"] = unserialize(base64_decode($match->board_state));
            $data["status"] = "playing";
        } else {
            $data["match_status"] = Match::ACTIVE;
            $data["status"] = "waiting";
            $data["board"] = new Board_model(7);
        }
        
        $data["data"] = $data;
        
        $this->load->view("match/board_view", $data);
    }



}

