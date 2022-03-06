import React, { useEffect, useState } from "react";
import Form from 'react-bootstrap/Form'
import Button from 'react-bootstrap/Button';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { useNavigate, useParams } from 'react-router-dom'
//import axios from 'axios';
import axios from "./axios-instance";
import Swal from 'sweetalert2';

export default function EditUser() {
  const navigate = useNavigate();

  const { id } = useParams()

  const [description, setDescription] = useState("")
  const [assignee, setAssignee] = useState("")
  const [priority, setPriority] = useState("")
  const [duedate, setDueDate] = useState("")
  const [status, setStatus] = useState("")
  const [isadmin, setIsAdmin] = useState("")
  const [validationError,setValidationError] = useState({})
  const [isDisabled, setIsDisabled] = useState(true);

  useEffect(()=>{
    fetchTask()
  }, [])

  const fetchTask = async () => {
    await axios.get(`/api/tasks/${id}`).then(({data})=>{
      const { Description, Assignee, Priority, DueDate, Status, IsAdmin } = data.task
      setDescription(Description)
	  setAssignee(Assignee)
	  setPriority(Priority)
	  setDueDate(DueDate)
	  setStatus(Status)	  
	  setIsAdmin(IsAdmin)
	  if (IsAdmin === '1')
		setIsDisabled(false);
    }).catch(({response:{data}})=>{
      Swal.fire({
        text:data.message,
        icon:"error"
      })
    })
  }

  const updateTask = async (e) => {
    e.preventDefault();

    const formData = new FormData()
    formData.append('_method', 'PATCH');
	if (isadmin)
      formData.append('Assignee', assignee)
    formData.append('Description', description)
	formData.append('Priority', priority)
	formData.append('DueDate', duedate)
	formData.append('Status', status)

    await axios.post(`/api/tasks/${id}`, formData).then(({data})=>{
      Swal.fire({
        icon:"success",
        text:data.message
      })
      navigate("/")
    }).catch(({response})=>{
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
              <h4 className="card-title">Update Task</h4>
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
                <Form onSubmit={updateTask}>                  
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
                            <Form.Select value={priority} onChange={(event)=>{
                              setPriority(event.target.value)
                            }}>
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
                            <Form.Control type="text" disabled={isDisabled} value={assignee} onChange={(event)=>{
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
                            <Form.Select value={status} onChange={(event)=>{
                              setStatus(event.target.value)
                            }}>
							  <option value="Pending">Pending</option>
							  <option value="In Progress">In Progress</option>
							  <option value="Complete">Complete</option>
							</Form.Select>
                        </Form.Group>
                      </Col>  
                  </Row>				  
                  <Button variant="primary" className="mt-2" size="lg" block="block" type="submit">
                    Update
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
