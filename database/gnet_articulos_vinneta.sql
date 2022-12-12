

IF EXISTS (SELECT * FROM sys.all_objects WHERE object_id = OBJECT_ID(N'[dbo].[gnet_articulos_vinneta]') AND type IN ('U'))
	DROP TABLE [dbo].[gnet_articulos_vinneta]
GO

CREATE TABLE "dbo"."gnet_articulos_vinneta"
(
   ID int IDENTITY(1,1) PRIMARY KEY NOT NULL,
   [ARTICULO] nvarchar(20) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
	 [UNIDAD] nvarchar(5) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
	 [VALOR] nvarchar(20) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
	 [FECHAA] datetime  NOT NULL
);

ALTER TABLE [dbo].[gnet_articulos_vinneta] SET (LOCK_ESCALATION = TABLE)
GO
