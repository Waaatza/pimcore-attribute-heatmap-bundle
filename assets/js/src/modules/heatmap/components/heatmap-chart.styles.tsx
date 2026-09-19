import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => ({
  controls: css`
    margin-bottom: ${token.marginMD}px;
  `,
  filter: css`
    min-width: 220px;
  `,
  chart: css`
    display: flex;
    flex-direction: column;
    gap: ${token.marginSM}px;
  `,
  row: css`
    display: grid;
    grid-template-columns: minmax(160px, 280px) minmax(240px, 1fr) 64px;
    align-items: center;
    gap: ${token.marginSM}px;
  `,
  label: css`
    min-width: 0;
  `,
  title: css`
    overflow: hidden;
    color: ${token.colorText};
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,
  meta: css`
    overflow: hidden;
    color: ${token.colorTextTertiary};
    font-size: ${token.fontSizeSM}px;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,
  track: css`
    height: 20px;
    overflow: hidden;
    border-radius: ${token.borderRadiusSM}px;
    background: ${token.colorFillSecondary};
  `,
  bar: css`
    height: 100%;
    min-width: 0;
    border-radius: ${token.borderRadiusSM}px;
    transition: width ${token.motionDurationMid};
  `,
  barUsed: css`
    background: ${token.colorSuccess};
  `,
  barPartiallyUsed: css`
    background: ${token.colorWarning};
  `,
  barUnused: css`
    background: ${token.colorError};
  `,
  barNotAnalyzable: css`
    background: ${token.colorTextTertiary};
  `,
  value: css`
    color: ${token.colorTextSecondary};
    font-variant-numeric: tabular-nums;
    text-align: right;
  `,
  empty: css`
    margin-top: ${token.marginSM}px;
  `,
  responsive: css`
    @media (max-width: 720px) {
      grid-template-columns: minmax(120px, 1fr) minmax(120px, 2fr) 52px;
    }
  `
}))
