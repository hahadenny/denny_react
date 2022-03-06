import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import Button from 'react-bootstrap/Button'
//import axios from 'axios';
import axios from "./axios-instance";
import Swal from 'sweetalert2'

export default function List() {

    const [tasks, setTasks] = useState([])

    useEffect(()=>{
        fetchTasks() 
    },[])

    const fetchTasks = async () => {
        await axios.get(`/api/tasks`).then(({data})=>{
            setTasks(data)
        })
    }

    const deleteTask = async (id) => {
        const isConfirm = await Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            return result.isConfirmed
          });

          if(!isConfirm){
            return;
          }

          await axios.delete(`/api/tasks/${id}`).then(({data})=>{
            Swal.fire({
                icon:"success",
                text:data.message
            })
            fetchTasks()
          }).catch(({response:{data}})=>{
            Swal.fire({
                text:data.message,
                icon:"error"
            })
          })
    }

    return (
      <div className="container">
          <div className="row">
            <div className='col-12'>
                <Link className='btn btn-primary mb-2 float-end' to={"/task/create"}>
                    Create Task
                </Link>
            </div>
            <div className="col-12">
                <div className="card card-body">
                    <div className="table-responsive">
                        <table className="table table-bordered mb-0 text-center">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Task</th>
                                    <th>Creator</th>
				    <th>Priority</th>
				    <th>Due Date</th>
				    <th>Assignee</th>
				    <th>Add Date</th>
				    <th>Last Update</th>
				    <th>Status</th>
				    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {
                                    tasks.length > 0 && (
                                        tasks.map((row, key)=>(
                                            <tr key={key}>
                                                <td>{row.Id}</td>
						<td>{row.Description}</td>
                                                <td>{row.Creator}</td>
						<td>{row.Priority}</td>
						<td>{row.DueDate}</td>
						<td>{row.Assignee}</td>
						<td>{row.DateAdded}</td>
						<td>{row.DateUpdated}</td>
						<td>{row.Status}</td>
                                                <td>
                                                    <Link to={`/task/edit/${row.Id}`} className='btn btn-success me-2'>
                                                        Edit
                                                    </Link>
                                                    <Button variant="danger" onClick={()=>deleteTask(row.Id)}>
                                                        Delete
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))
                                    )
                                }
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>
      </div>
    )
}
