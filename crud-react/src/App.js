import * as React from "react";
import Navbar from "react-bootstrap/Navbar";
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import "bootstrap/dist/css/bootstrap.css";

import { BrowserRouter as Router , Routes, Route, Link } from "react-router-dom";

import EditTask from "./components/task/edit.component";
import TaskList from "./components/task/list.component";
import CreateTask from "./components/task/create.component";

function App() {
  return (<Router>
    <Navbar bg="primary">
      <Container>
        <Link to={"/"} className="navbar-brand text-white">
          Denny Task
        </Link>
      </Container>
    </Navbar>

    <Container className="mt-5">
      <Row>
        <Col md={12}>
          <Routes>
            <Route path="/task/create" element={<CreateTask />} />
            <Route path="/task/edit/:id" element={<EditTask />} />
            <Route exact path='/' element={<TaskList />} />
          </Routes>
        </Col>
      </Row>
    </Container>
  </Router>);
}

export default App;
