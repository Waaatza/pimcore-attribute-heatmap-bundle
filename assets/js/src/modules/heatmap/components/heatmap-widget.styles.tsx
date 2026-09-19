import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => ({
  section: css`
    margin-bottom: ${token.margin}px;
  `,
  select: css`
    width: 320px;
  `,
  progressSummary: css`
    width: 120px;
  `,
  progressLarge: css`
    width: 360px;
    max-width: 100%;
  `,
  phaseCaption: css`
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
  `,
  legend: css`
    display: flex;
    gap: ${token.margin}px;
    margin-bottom: ${token.marginSM}px;
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
  `,
  legendDot: css`
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: ${token.borderRadiusSM}px;
  `,
  group: css`
    margin-bottom: ${token.margin}px;
  `,
  groupCount: css`
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextTertiary};
  `,
  tiles: css`
    display: flex;
    flex-wrap: wrap;
    gap: ${token.marginXS}px;
    margin-top: ${token.marginXS}px;
  `,
  tile: css`
    width: 150px;
    min-width: 150px;
    padding: ${token.paddingXS}px ${token.paddingSM}px;
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid transparent;
    background: ${token.colorBgContainer};
    box-sizing: border-box;
  `,
  tileTitle: css`
    font-size: ${token.fontSizeSM}px;
    line-height: ${token.lineHeightSM}px;
    color: ${token.colorTextSecondary};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  `,
  tileValue: css`
    font-size: ${token.fontSizeLG}px;
    font-weight: ${token.fontWeightStrong};
    margin-top: ${token.marginXXS}px;
  `,
  tileUsed: css`
    border-color: ${token.colorSuccess};
    background: ${token.colorSuccessBg};
  `,
  tilePartiallyUsed: css`
    border-color: ${token.colorWarning};
    background: ${token.colorWarningBg};
  `,
  tileUnused: css`
    border-color: ${token.colorError};
    background: ${token.colorErrorBg};
  `,
  tileNotAnalyzable: css`
    border-color: ${token.colorTextTertiary};
    background: ${token.colorFillTertiary};
  `,
  valueUsed: css`
    color: ${token.colorSuccess};
  `,
  valuePartiallyUsed: css`
    color: ${token.colorWarning};
  `,
  valueUnused: css`
    color: ${token.colorError};
  `,
  valueNotAnalyzable: css`
    color: ${token.colorTextTertiary};
  `,
  dotUsed: css`
    background: ${token.colorSuccess};
  `,
  dotPartiallyUsed: css`
    background: ${token.colorWarning};
  `,
  dotUnused: css`
    background: ${token.colorError};
  `,
  dotNotAnalyzable: css`
    background: ${token.colorTextTertiary};
  `
}))