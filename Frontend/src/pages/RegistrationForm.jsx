//jsx id="reg01"
import { useState } from 'react'

const RegistrationForm = ({ eventId }) => {
  const [form, setForm] = useState({
    name: '',
    email: '',
    phone: '',
  })

  const submit = async (e) => {
    e.preventDefault()

    await fetch(
      'http://localhost:5000/api/registration',
      {
        method: 'POST',
        headers: {
          'Content-Type':
            'application/json',
        },
        body: JSON.stringify({
          ...form,
          eventId,
        }),
      }
    )

    alert('Registered successfully')
  }

  return (
    <form onSubmit={submit} className="p-4">
      <input
        placeholder="Name"
        onChange={(e) =>
          setForm({
            ...form,
            name: e.target.value,
          })
        }
      />

      <input
        placeholder="Email"
        onChange={(e) =>
          setForm({
            ...form,
            email: e.target.value,
          })
        }
      />

      <input
        placeholder="Phone"
        onChange={(e) =>
          setForm({
            ...form,
            phone: e.target.value,
          })
        }
      />

      <button>Register</button>
    </form>
  )
}

export default RegistrationForm
