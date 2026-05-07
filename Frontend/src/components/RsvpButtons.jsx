import { useState } from 'react'

const RsvpButtons = ({ eventId, guestId }) => {
  const [loading, setLoading] = useState(false)

  const sendResponse = async (status) => {
    setLoading(true)

    try {
      await fetch('http://localhost:5000/api/rsvp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          eventId,
          guestId,
          status,
        }),
      })

      alert(`RSVP ${status}`)
    } catch (err) {
      alert('Failed')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="flex gap-2">
      <button
        disabled={loading}
        onClick={() => sendResponse('accepted')}
        className="px-3 py-1 bg-green-600 text-white rounded"
      >
        Accept
      </button>

      <button
        disabled={loading}
        onClick={() => sendResponse('declined')}
        className="px-3 py-1 bg-red-600 text-white rounded"
      >
        Decline
      </button>
    </div>
  )
}

export default RsvpButtons