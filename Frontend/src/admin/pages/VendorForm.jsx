//jsx id="vui01"
import { useState } from 'react'

const VendorForm = () => {
  const [form, setForm] = useState({
    name: '',
    serviceType: '',
    phone: '',
    email: '',
    address: '',
  })

  const submit = async (e) => {
    e.preventDefault()

    await fetch(
      'http://localhost:5000/api/vendors',
      {
        method: 'POST',
        headers: {
          'Content-Type':
            'application/json',
        },
        body: JSON.stringify(form),
      }
    )

    alert('Vendor added')
  }

  return (
    <form onSubmit={submit} className="p-4">
      <input
        placeholder="Vendor Name"
        onChange={(e) =>
          setForm({
            ...form,
            name: e.target.value,
          })
        }
      />

      <select
        onChange={(e) =>
          setForm({
            ...form,
            serviceType:
              e.target.value,
          })
        }
      >
        <option value="">
          Select Service
        </option>
        <option value="catering">
          Catering
        </option>
        <option value="decoration">
          Decoration
        </option>
        <option value="music">
          Music
        </option>
      </select>

      <input
        placeholder="Phone"
        onChange={(e) =>
          setForm({
            ...form,
            phone: e.target.value,
          })
        }
      />

      <button>Add Vendor</button>
    </form>
  )
}

export default VendorForm
