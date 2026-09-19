import { useEffect, useState } from 'react'
import { getPrefix } from '@pimcore/studio-ui-bundle/api'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import type { AttributeHeatmapResult } from '../api/heatmap-api'

export interface ProgressPayload {
  percent: number
  phase: string
}

export type HeatmapPhase = 'collect' | 'count' | 'objects' | 'hydrate' | 'done' | 'idle'

export interface HeatmapStreamState {
  progress: number
  phase: HeatmapPhase
  data: AttributeHeatmapResult | null
  error: string | null
  isFetching: boolean
}

const parseSseChunk = (chunk: string): { event: string; data: string } | null => {
  if (chunk.trim() === '') {
    return null
  }

  let event = 'message'
  let data = ''

  for (const line of chunk.split('\n')) {
    if (line.startsWith('event: ')) {
      event = line.slice('event: '.length)
    } else if (line.startsWith('data: ')) {
      data += line.slice('data: '.length)
    }
  }

  return { event, data }
}

export const useHeatmapStream = (classId: string | null): HeatmapStreamState => {
  const { t } = useTranslation()
  const [progress, setProgress] = useState(0)
  const [phase, setPhase] = useState<HeatmapPhase>('idle')
  const [data, setData] = useState<AttributeHeatmapResult | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [isFetching, setIsFetching] = useState(false)

  useEffect(() => {
    if (classId === null) {
      return
    }

    const abortController = new AbortController()
    const url = `${getPrefix()}/bundle/attribute-heatmap/classes/${classId}/heatmap/stream`

    setProgress(0)
    setPhase('collect')
    setData(null)
    setError(null)
    setIsFetching(true)

    const handleEvent = (event: string, payload: unknown): void => {
      if (event === 'progress') {
        const value = payload as ProgressPayload
        setProgress(value.percent)
        setPhase(value.phase as HeatmapPhase)
      } else if (event === 'result') {
        setData(payload as AttributeHeatmapResult)
        setIsFetching(false)
      } else if (event === 'error') {
        const value = payload as { message: string }
        setError(value.message)
        setIsFetching(false)
      }
    }

    const run = async (): Promise<void> => {
      try {
        const response = await fetch(url, {
          method: 'GET',
          headers: { Accept: 'text/event-stream' },
          credentials: 'same-origin',
          signal: abortController.signal
        })

        if (!response.ok || response.body === null) {
          throw new Error(t('attribute-heatmap.stream.start-error', { status: response.status }))
        }

        const reader = response.body.getReader()
        const decoder = new TextDecoder()
        const buffer: string[] = []

        while (true) {
          const { done, value } = await reader.read()

          if (done) {
            setIsFetching(false)
            break
          }

          buffer.push(decoder.decode(value, { stream: true }))

          let joined = buffer.join('')
          buffer.length = 0

          let boundary = joined.indexOf('\n\n')

          while (boundary !== -1) {
            const chunk = joined.slice(0, boundary)
            joined = joined.slice(boundary + 2)

            const parsed = parseSseChunk(chunk)

            if (parsed) {
              handleEvent(parsed.event, JSON.parse(parsed.data))
            }

            boundary = joined.indexOf('\n\n')
          }

          if (joined !== '') {
            buffer.push(joined)
          }
        }
      } catch (err) {
        if (!abortController.signal.aborted) {
          setError(err instanceof Error ? err.message : t('attribute-heatmap.stream.unexpected-error'))
          setIsFetching(false)
        }
      }
    }

    void run()

    return () => abortController.abort()
  }, [classId, t])

  return { progress, phase, data, error, isFetching }
}
