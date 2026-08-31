using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace BrilliantEngineering.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class RenameProductBrandLogoToBackgroundImage : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "LogoPath",
                table: "ProductBrands",
                newName: "BackgroundImagePath");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "BackgroundImagePath",
                table: "ProductBrands",
                newName: "LogoPath");
        }
    }
}
