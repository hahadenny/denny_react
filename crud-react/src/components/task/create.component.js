import React, { useState } from "react";
import Form from 'react-bootstrap/Form'
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
//import axios from 'axios'
import axios from "./axios-instance";
import Swal from 'sweetalert2';
import { useNavigate } from 'react-router-dom'

export default function CreateTask() {
  const navigate = useNavigate();
 
  const [description, setDescription] = useState("")
  const [assignee, setAssignee] = useState("")
  const [priority, setPriority] = useState("")
  const [duedate, setDueDate] = useState("")
  const [status, setStatus] = useState("")
  const [validationError,setValidationError] = useState({})

  const createTask = async (e) => {
    e.preventDefault();

    const formData = new FormData()
    formData.append('Description', description)
	formData.append('Assignee', assignee)
	formData.append('Priority', priority)
	formData.append('DueDate', duedate)
	formData.append('Status', status)

    await axios.post('/api/tasks', formData).then(({data})=>{
      Swal.fire({
        icon:"success",
        text:data.message
      })
      navigate("/")
    }).catch(({response})=>{
      console.log(response);
      if(response.status===422){
        setValidationError(response.data.errors)
      }else{
        Swal.fire({
          text:response.data.message,
          icon:"error"
        })
      }
    })
  }

  return (
    <div className="container">
      <div className="row justify-content-center">
        <div className="col-12 col-sm-12 col-md-6">
          <div className="card">
            <div className="card-body">
              <h4 className="card-title">Create Task</h4>
              <hr />
              <div className="form-wrapper">
                {
                  Object.keys(validationError).length > 0 && (
                    <div className="row">
                      <div className="col-12">
                        <div className="alert alert-danger">
                          <ul className="mb-0">
                            {
                              Object.entries(validationError).map(([key, value])=>(
                                <li key={key}>{value}</li>   
                              ))
                            }
                          </ul>
                        </div>
                      </div>
                    </div>
                  )
                }
                <Form onSubmit={createTask}>                  
                  <Row className="my-3">
                      <Col>
                        <Form.Group controlId="Description">
                            <Form.Label>Description</Form.Label>
                            <Form.Control as="textarea" rows={3} value={description} onChange={(event)=>{
                              setDescription(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>
                  </Row>
				  <Row className="my-3"> 
                      <Col>
                        <Form.Group controlId="Priority">
                            <Form.Label>Priority</Form.Label>
                            <Form.Select onChange={(event)=>{
                              setPriority(event.target.value)
                            }}>
							  <option value="">Select</option>
							  <option value="Low">Low</option>
							  <option value="Medium">Medium</option>
							  <option value="High">High</option>
							  <option value="Critical">Critical</option>
							</Form.Select>
                        </Form.Group>
                      </Col>  
                  </Row>
				  <Row className="my-3"> 
                      <Col>
                        <Form.Group controlId="Assignee">
                            <Form.Label>Assignee</Form.Label>
                            <Form.Control type="text" value={assignee} onChange={(event)=>{
                              setAssignee(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>  
                  </Row>
				  <Row className="my-3"> 
                      <Col>
                        <Form.Group controlId="DueDate">
                            <Form.Label>Due Date (YYYY-MM-DD)</Form.Label>
                            <Form.Control type="text" value={duedate} onChange={(event)=>{
                              setDueDate(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>  
                  </Row>
				  <Row className="my-3"> 
                      <Col>
                        <Form.Group controlId="Status">
                            <Form.Label>Status</Form.Label>
                            <Form.Select onChange={(event)=>{
                              setStatus(event.target.value)
                            }}>
							  <option value="">Select</option>
							  <option value="Pending">Pending</option>
							  <option value="In Progress">In Progress</option>
							  <option value="Complete">Complete</option>
							</Form.Select>
                        </Form.Group>
                      </Col>  
                  </Row>
                  <Button variant="primary" className="mt-2" size="lg" block="block" type="submit">
                    Save
                  </Button>
                </Form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
