<?php
// Copyright (C) 2012 by Mike Smit
// License: Creative Commons BY-SA Version 3.0 
// This code is from a StackOverflow answer by Mike Smit
// See: http://stackoverflow.com/questions/994041/how-can-i-put-the-results-of-a-mysqli-prepared-statement-into-an-associative-arr

class database {
        private $stmt;
        private $mysqli;
        private $query;
        private $params = array();
        private $type;

        public function __set($name, $value) {
            switch ($name) {
                case 'params':
                    $this->params = $value;
                    break;
                case 'query':
                    $this->query = $value;
                    break;
                case 'type':
                    $this->type = $value;
                    break;
                default:
                    break;
            }
        }

        public function __get($name) {
            if ($name !== "mysqli" && $name !== "stmt")
                return $this->$name;
        }

        public function __construct() {
			include ('db_info.php');
            $this->mysqli = new mysqli($sql_server, $sql_user, $sql_password, $sql_database);
            $this->stmt = $this->mysqli->stmt_init();
        }

        private function close_con($bool) {
            if ($bool) {
                $this->stmt->free_result();
            }
            $this->stmt->close();
            $this->mysqli->close();
        }

        private function nofetch() {
            $this->stmt->prepare($this->query);
            $bind_names[] = $this->type;
            for ($i = 0; $i < count($this->params); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $this->params[$i];
                $bind_names[] = &$$bind_name;
            }
            call_user_func_array(array($this->stmt, "bind_param"), $bind_names);

            if ($this->stmt->execute()) {

                $this->close_con(false);
                return true;
            }
            $this->close_con(false);
            return false;
        }

        public function insert() {
            if ($this->nofetch()) {
                return true;
            }
            return false;
        }

        public function update() {
            if ($this->nofetch()) {
                return true;
            }
            return false;
        }

        public function delete() {
            if ($this->nofetch()) {
                return true;
            }
            return false;
        }

        public function fetch() {
            $result_out = array();
            $this->stmt->prepare($this->query);
            $bind_names[] = $this->type;
            if (count($this->params) > 0) {
                for ($i = 0; $i < count($this->params); $i++) {
                    $bind_name = 'bind' . $i;
                    $$bind_name = $this->params[$i];
                    $bind_names[] = &$$bind_name;
                }
                call_user_func_array(array($this->stmt, "bind_param"), $bind_names);
            }
            if ($this->stmt->execute()) {
                $result = $this->stmt->result_metadata();
                $cols = $result->fetch_fields();
                foreach ($cols as $col) {

                    $name = str_replace("-", "_", $col->name);

                    $$name = null;
                    if ($name == null)
                        $name = 'name';
                    $bindarray[$name] = &$$name;
                }

                call_user_func_array(array($this->stmt, 'bind_result'), $bindarray);
                $this->stmt->store_result();
                $copy = create_function('$a', 'return $a;');
                while ($this->stmt->fetch()) {
                    $result_out[] = array_map($copy, $bindarray);
                }
            }
            $this->close_con(true);
            return $result_out;
        }

    }
?>