export const extractApiErrors = (error, fallbackMessage = 'Something went wrong.') => {
  const payload = error?.response?.data

  if (!payload) {
    return [fallbackMessage]
  }

  const message = payload.message ?? payload.errors

  if (Array.isArray(message)) {
    return message
  }

  if (message && typeof message === 'object') {
    return Object.values(message).flat().map((item) => String(item))
  }

  if (typeof message === 'string' && message.trim()) {
    return [message]
  }

  return [fallbackMessage]
}
